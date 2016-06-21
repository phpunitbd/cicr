#!/bin/sh
if [ `whoami` != 'root' ] ;then
  echo you must be root
  exit 1
fi

#
# Usage: run ./install.sh as root
# The script will ask some questions, that can be skipped by setting some env variables
# PROD_PATH: path where to install magento (root will be $PROD_PATH/magento ; scripts will go to $PROD_PATH/scripts)
# DOMAIN: domain name for magento. Apache config file will be /etc/apache2/sites-avaiable/$DOMAIN
#
# Additional vars may be set:
# GIT: git repo for exporting files, defaults to distrib.data.fr
# IP: IP apache will listen on, defaults to *:80
# PROTO: defaults to http, set https to use https in magento
# ADDSSLENGINE: if set to 1, add SSLEngine directives to apache ; needed if https is handled by apache (not using nginx reverse proxy)
# MAILTO: to set recipient of info mail
# BRANCH: default to prod, sets git branch from which files has to be fetched
# FORCE_DBNAME: use given database, instead of computing one from domain
# USE_NGINX: set to 1 to use (and configure) nginx
# SEPARATE_IP: set to 1 to have 2 separate sever{} entries in nginx
# EXTERNAL_IP: set ip to the external site IP (if SEPARATE_IP)
# INTERNAL_IP: set ip to the internal site IP (if SEPARATE_IP)
#

if [ ! -z "$DEBUG" ]; then
    set -x
fi

if [ -z "$PROD_PATH" ] ; then
  echo -n "Enter path to install: "
  read prod_path
else
  prod_path="$PROD_PATH"
fi
if [ -z "$DOMAIN" ] ; then
  echo -n "Enter domain name to use: "
  read domain
else
  domain="$DOMAIN"
fi
if [ -z "$GIT" ] ; then
  git_repo="git-ro@distrib.data.fr:/var/lib/git/icrc-magento.git/"
else
  git_repo="$GIT"
fi

if [ -z "$IP" ] ; then
    if [ "$USE_NGINX" = "1" ]; then
        IP="*:8080"
    else
        IP="*:80"
    fi
fi

if [ -z "$PROTO" ] ; then
    proto=http
else
    proto="$PROTO"
fi

if [ -z "$MAILTO" ] ; then
  mailto='support-hosting@data.fr'
else
  mailto="$MAILTO"
fi

if [ -z "$BRANCH" ]; then
  branch="merged-prod"
else
  branch="$BRANCH"
fi

if [ -z "$INSTALL_BRANCH" ]; then
    install_branch="magento"
else
    install_branch="$INSTALL_BRANCH"
fi

if [ ! -z "$SEPARATE_IP" ]; then
    if [ -z "$INTERNAL_IP" ]; then
	echo "Cannot have SEPARATE_IP without defining INTERNAL_IP"
	exit 1
    fi
    if [ -z "$EXTERNAL_IP" ]; then
	echo "Cannot have SEPARATE_IP without defining EXTERNAL_IP"
	exit 1
    fi
fi

dbname=`echo $domain | sed s/.data.fr\$// | tr - _ | tr . _ | head -c 16`
if [ ! -z "$FORCE_DBNAME" ]; then
    dbname="$FORCE_DBNAME"
fi

test -x /usr/bin/curl || apt-get install -q curl
test -x /usr/bin/host || apt-get install -q bind9-host
dpkg -l php5-curl | grep -q ^ii || apt-get install php5-curl

echo "Installing '$domain' ($IP/$proto) in '$prod_path' on DB '$dbname'."
echo "Is that correct [y/n] ?"
read correct
if [ "$correct" != 'y' ] ; then
  exit 1
fi
echo Installing

abort()
{
  echo Abort
  exit 1
}

db_exists()
{
  echo Database "$1" exists, please install on a fresh new one or manually drop this one
  exit 1
}

prod_exists()
{
  echo Directory "$1" exists, please install on a fresh new one or manually delete this one
  exit 1
}

## Check if we resolve on loopback
ip_for_domain=`host ${domain} | grep -v IPv6 | awk '{print $4}'`
ip6_for_domain=`host ${domain} | grep IPv6 | awk '{print $5}'`
grep_args=""
for ip in $ip_for_domain ; do grep_args="${grep_args} -e $ip" ; done
for ip in $ip6_for_domain ; do grep_args="${grep_args} -e $ip" ; done
if ! /sbin/ifconfig | grep -q $grep_args ; then
  if ! grep -q ${domain} /etc/hosts ; then
    loopback="127."`echo ${domain} | cksum | cut -c 1-2`"."`echo ${domain} | cksum | cut -c 3-4`"."`echo ${domain} | cksum | cut -c 5-6`
    echo "${loopback} ${domain}" >> /etc/hosts
    echo "Added ${loopback} for ${domain} into /etc/hosts"
  fi
fi

sysdbuser=`grep ^user /etc/mysql/debian.cnf | head -n 1 | awk '{ print $3 }'`
sysdbpass=`grep ^password /etc/mysql/debian.cnf | head -n 1 | awk '{ print $3 }'`

dbpwd=`dd 2>/dev/null if=/dev/urandom bs=1k count=1 | shasum | cut -d\  -f1`
adminpasswd=`dd 2>/dev/null if=/dev/urandom bs=16 count=1 | openssl enc -base64 | head -c8`
apikey=`dd 2>/dev/null if=/dev/urandom bs=16 count=1 | openssl enc -base64 | head -c8`
adminfront=a`dd 2>/dev/null if=/dev/urandom bs=16 count=1 | xxd -l 100 -ps -c 8 | head -1`

perl -e "exit 1 if not (\"${adminpasswd}\" =~ /[0-9]/)" || adminpasswd="${adminpasswd}1"

test -d ${prod_path} && test -f ${prod_path}/magento/mage && prod_exists ${prod_path}
mysql -u $sysdbuser -p$sysdbpass ${dbname} -e "select 1" > /dev/null 2> /dev/null && db_exists ${dbname}

if [ "$USE_NGINX" = "1" ]; then
  if [ "$proto" = "https" ]; then
    nginx_port=443
    nginx_set_header="proxy_set_header	X-Forwarded-Proto	https;"
    nginx_use_ssl="ssl on; ssl_certificate      ssl/server.crt; ssl_certificate_key  ssl/server.key; add_header Strict-Transport-Security max-age=631138519;"
    nginx_use_ssl_external="ssl on; ssl_certificate      ssl/shop_icrc_org.crt; ssl_certificate_key  ssl/server.key;"
    nginx_use_ssl_internal="ssl on; ssl_certificate      ssl/internalshop_icrc_org.crt; ssl_certificate_key  ssl/server.key;"
  else
    nginx_port=80
  fi
  if [ -z "$SEPARATE_IP"]; then
      cat > /etc/nginx/sites-enabled/${domain} << EOF
server {
  server_name ${domain} internal${domain};
  root ${prod_path}/magento;
  listen ${nginx_port};
  ${nginx_use_ssl}
  
  location = /robots.txt {
    access_log off; log_not_found off;
    root	${prod_path}/magento;
    sendfile on;
  }

  location / {
    rewrite ^/fr\$          /?___store=fr        break;
    rewrite ^/fr/(.*)      /\$1?___store=fr      break;
    rewrite ^/en/(.*)      /\$1?___store=default break;
    rewrite ^/en\$          /?___store=default   break;
    rewrite ^/default/(.*) /\$1?___store=default break;
    rewrite ^/default\$     /?___store=default   break;
    proxy_pass		http://127.0.0.1:8080/;
    proxy_set_header	Host	\$http_host;
    proxy_set_header    X-Forwarded-For  \$remote_addr;
    proxy_read_timeout  1800;
    $nginx_set_header
  }

  # Disable access to .git and .htaccess
  location ~ \.(git|htaccess) { deny all; }

  # Direct access for static files
  location ~ ^/(js|media|skin|var/datastudio)/ {
    root	${prod_path}/magento;
    sendfile on;
  }

  # Disable some paths
  location ~ ^/(app|includes|lib|media/downloadable|pkginfo|report/config.xml|var|munin|scripts|shell)/ {
     internal;
  }

}
EOF
  else
      ## SEPARATE_IP: this is for prod server
      cat > /etc/nginx/sites-enabled/${domain} << EOF
server {
  server_name ${domain};
  root ${prod_path}/magento;
  listen ${EXTERNAL_IP}:${nginx_port};
  ${nginx_use_ssl_external}
  tcp_nopush on;
  tcp_nodelay on;
  
  location = /robots.txt {
    access_log off; log_not_found off;
    root	${prod_path}/magento;
  }

  location / {
    rewrite ^/fr\$          /?___store=fr        break;
    rewrite ^/fr/(.*)      /\$1?___store=fr      break;
    rewrite ^/en/(.*)      /\$1?___store=default break;
    rewrite ^/en\$          /?___store=default   break;
    rewrite ^/default/(.*) /\$1?___store=default break;
    rewrite ^/default\$     /?___store=default   break;
    proxy_pass             http://127.0.0.1:8080/;
    proxy_set_header       Host	                \$http_host;
    proxy_set_header       X-Forwarded-For      \$remote_addr;
    $nginx_set_header
  }

  # Disable access to .git and .htaccess
  location ~ \.(git|htaccess) { deny all; }

  # Direct access for static files
  location ~ ^/(js|media|skin|var/datastudio)/ {
    root	${prod_path}/magento;
  }

  # Disable some paths
  location ~ ^/(app|includes|lib|media/downloadable|pkginfo|report/config.xml|var|munin|scripts|shell)/ {
     internal;
  }

}

server {
  server_name internal${domain};
  root ${prod_path}/magento;
  listen ${INTERNAL_IP}:${nginx_port};
  ${nginx_use_ssl_internal}
  
  location = /robots.txt {
    access_log off; log_not_found off;
    root	${prod_path}/magento;
  }

  location / {
    proxy_pass		http://127.0.0.1:8080/;
    proxy_set_header	Host	\$http_host;
    proxy_set_header  X-Forwarded-For  \$remote_addr;
    $nginx_set_header
  }

  # Disable access to .git and .htaccess
  location ~ \.(git|htaccess) { deny all; }

  # Direct access for static files
  location ~ ^/(js|media|skin|var/datastudio)/ {
    root	${prod_path}/magento;
  }

  # Disable some paths
  location ~ ^/(app|includes|lib|media/downloadable|pkginfo|report/config.xml|var|munin|scripts|shell)/ {
     internal;
  }

}
EOF
  fi
  if [ "$proto" = "https" ]; then
    cat > /etc/nginx/sites-enabled/${domain}-plain << EOF
server {
  server_name ${domain};
  root ${prod_path}/magento;
  listen 80;
  
  location / {
    proxy_pass		http://127.0.0.1:8080/;
    proxy_set_header	Host	\$http_host;
  }

  # Disable access to .git and .htaccess
  location ~ \.(git|htaccess) { deny all; }
}
EOF
  fi
  /etc/init.d/nginx reload || abort
fi

mkdir -p $prod_path/magento
git clone --branch=${install_branch} ${git_repo} $prod_path/magento || abort
cd $prod_path/magento
./mage mage-setup .
chown -R www-data var media sitemap
mkdir -p $prod_path/magento/app/code/local/Data/Icrc
touch $prod_path/magento/app/code/local/Data/Icrc/prepend.php

mysql 2> /dev/null -u $sysdbuser -p$sysdbpass <<EOF
DROP USER '${dbname}'@'localhost';
EOF

mysql -u $sysdbuser -p$sysdbpass <<EOF
CREATE USER '${dbname}'@'localhost' IDENTIFIED BY '${dbpwd}';
GRANT USAGE ON * . * TO '${dbname}'@'localhost' IDENTIFIED BY '${dbpwd}' WITH MAX_QUERIES_PER_HOUR 0 MAX_CONNECTIONS_PER_HOUR 0 MAX_UPDATES_PER_HOUR 0 MAX_USER_CONNECTIONS 0 ;
CREATE DATABASE IF NOT EXISTS \`${dbname}\` ;
GRANT ALL PRIVILEGES ON \`${dbname}\` . * TO '${dbname}'@'localhost';
EOF

cat > /etc/apache2/sites-enabled/${domain}.conf << EOF
<VirtualHost ${IP}>
	ServerAdmin informatique@data.fr
	ServerName ${domain}
	ServerAlias internal${domain} www.${domain} www.internal${domain}
EOF

if [ "$ADDSSLENGINE" = "1" ]; then
  cat >> /etc/apache2/sites-enabled/${domain}.conf << EOF
	SSLEngine on
  SSLCertificateFile    /etc/apache2/ssl/cert.pem
  SSLCertificateKeyFile /etc/apache2/ssl/cert.key
	SSLCertificateChainFile /etc/apache2/ssl/chain.pem
EOF
fi

cat >> /etc/apache2/sites-enabled/${domain}.conf << EOF
	DocumentRoot ${prod_path}/magento
	<Directory />
		Options FollowSymLinks
		AllowOverride None
	</Directory>
	<Directory ${prod_path}/>
		Options Indexes FollowSymLinks
		Options -MultiViews
		AllowOverride None
		<IfVersion >= 2.4>
			Require all granted
		</IfVersion>
		<IfVersion < 2.4>
			Order allow,deny
			allow from all
		</IfVersion>
	</Directory>
  <Directory ${prod_path}/magento/>
    Options -Indexes
    AllowOverride All
    <IfVersion >= 2.4>
      Require all granted
    </IfVersion>
    <IfVersion < 2.4>
      Order allow,deny
      allow from all
    </IfVersion>
    php_value post_max_size 100M
    php_value upload_max_filesize 100M
    php_value auto_prepend_file ${prod_path}/magento/app/code/local/Data/Icrc/prepend.php
  </Directory>
  SetEnvIf Host internal${domain} MAGE_RUN_CODE=internal MAGE_RUN_TYPE=website
</VirtualHost>

EOF

if [ "$ADDSSLENGINE" = "1" ]; then
  pip=`echo ${IP} | sed s/:443/:80/`
  cat >> /etc/apache2/sites-enabled/${domain}.conf << EOF
<VirtualHost ${pip}>
	ServerAdmin informatique@data.fr
	ServerName ${domain}
	ServerAlias internal${domain}
	RewriteEngine On
	RewriteCond %{HTTPS} off
	RewriteRule (.*) https://%{HTTP_HOST}%{REQUEST_URI}
</VirtualHost>
EOF
fi

mkdir -p ${prod_path}/magento/app/code/local/Data/Icrc
touch ${prod_path}/magento/app/code/local/Data/Icrc/prepend.php

/etc/init.d/apache2 reload

# workaround for 'PHP Extensions "0" must be loaded' error
sed -i 's@<pdo_mysql/>@<pdo_mysql>1</pdo_mysql>@' ${prod_path}/magento/app/code/core/Mage/Install/etc/config.xml

php -f install.php -- \
  --license_agreement_accepted "yes" --locale "en_US" --timezone "Europe/Berlin" --default_currency "CHF" \
  --db_host localhost --db_name "${dbname}" --db_user "${dbname}" --db_pass ${dbpwd} \
  --url "http://${domain}" --secure_base_url "${proto}://${domain}"  \
  --use_rewrites "yes" --use_secure "yes" --use_secure_admin "yes" \
  --admin_firstname Bastien --admin_lastname DUREL --admin_email support@data.fr \
  --admin_username dataadmin --admin_password "${adminpasswd}" \
  --admin_frontname "${adminfront}" || exit 2

cron=`echo ${domain} | tr -s . - `

cat > /etc/cron.d/magento-${cron} <<EOF
MAILTO=support@data.fr
* * * * * www-data /bin/sh "${prod_path}/magento/cron.sh"
09,39 *     * * *     root   [ -x /usr/lib/php5/maxlifetime ] && [ -d /var/lib/php5 ] && find "${prod_path}/magento/var/session/" -depth -mindepth 1 -maxdepth 1 -type f -ignore_readdir_race -cmin +\$(/usr/lib/php5/maxlifetime) ! -execdir fuser -s {} 2>/dev/null \; -delete
EOF

if [ "$USE_NGINX" = "1" -a "$proto" = "https" ]; then
  rm -f /etc/nginx/sites-enabled/${domain}-plain
  cat >> /etc/nginx/sites-enabled/${domain} << EOF

server {
  server_name ${domain} internal${domain};
  root ${prod_path}/magento;
  listen 80;
  
  location / {
    rewrite ^(.*) https://\$host/\$1 redirect;
  }

  location /robots.txt {
    access_log off; log_not_found off;
    root	${prod_path}/magento;
  }

  # Disable access to .git and .htaccess
  location ~ \.(git|htaccess) { deny all; }
}
EOF
  /etc/init.d/nginx reload
fi

php <<EOF
<?php

include_once('app/Mage.php');
Mage::app();

\$role = Mage::getModel('api/roles')
  ->setName('data_icrc')
  ->setPid(false)
  ->setRoleType('G')
  ->save();

Mage::getModel("api/rules")
  ->setRoleId(\$role->getId())
  ->setResources(array('all'))
  ->saveRel();

\$user = Mage::getModel('api/user');
\$user->setData(array(
      'username' => 'datastudio',
      'firstname' => 'data',
      'lastname' => 'icrc',
      'email' => 'support@data.fr',
      'api_key' => '${apikey}',
      'api_key_confirmation' => '${apikey}',
      'is_active' => 1,
      'user_roles' => '',
      'assigned_user_role' => '',
      'role_name' => '',
      'roles' => array(\$role->getId())
      ));
\$user->save()->load(\$user->getId());

\$user->setRoleIds(array(\$role->getId()))
  ->setRoleUserId(\$user->getUserId())
  ->saveRelations();

Mage::getSingleton('index/indexer')->getProcessesCollection()->walk('reindexAll');
Mage::app()->cleanCache();

EOF

curl -o /dev/null -s "${proto}://${domain}"

echo Admin password is "${adminpasswd}" -- admin URL is "${proto}://${domain}/${adminfront}" -- SOAP datastudio password is "${apikey}"\
 | tee /dev/stderr | mail -s "Magento Installation" $mailto

cd ${prod_path}/magento
rm -f var/locks/*
rm $prod_path/magento/app/code/local/Data/Icrc/prepend.php
git checkout ${branch}
git checkout -- $prod_path/magento/app/code
git pull
chown -R www-data var media sitemap
rm -f var/locks/*
(cd shell ; php indexer.php reindexall) > /dev/null
curl -o /dev/null -s "${proto}://${domain}"

php <<EOF
<?php

include_once('app/Mage.php');
Mage::app();

\$conf = new Mage_Core_Model_Config();
\$int = Mage::getModel('core/website')->load('internal', 'code')->getId();
\$conf->saveConfig('web/unsecure/base_url', '${proto}://${domain}/', 'default', 0);
\$conf->saveConfig('web/secure/base_url', '${proto}://${domain}/', 'default', 0);
\$conf->saveConfig('web/unsecure/base_url', '${proto}://internal${domain}/', 'websites', \$int);
\$conf->saveConfig('web/secure/base_url', '${proto}://internal${domain}/', 'websites', \$int);
Echo "Setting internal url: '${proto}://internal${domain}/' for website \$int\n";

EOF

curl -o /dev/null -s "${proto}://${domain}"
rm -f ${prod_path}/magento/var/locks/index_process_*
(cd shell ; php indexer.php reindexall)
rm -f ${prod_path}/magento/var/locks/index_process_*
curl -o /dev/null -s "${proto}://${domain}"
rm -f ${prod_path}/magento/var/locks/index_process_*

cp ${prod_path}/magento/scripts/get_icrc_mods.sh ${prod_path}/
sed -i "s@__URL__@${proto}://${domain}@" ${prod_path}/get_icrc_mods.sh
sed -i "s@__PROD__@${prod_path}/magento@" ${prod_path}/get_icrc_mods.sh
