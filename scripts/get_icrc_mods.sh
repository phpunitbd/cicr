#!/bin/bash
prod=__PROD__
url=__URL__
u=__
me=`readlink -f $0`

if [ "x$1" = "x-tag" ]; then
    tag=1
    shift
else
    tag=0
fi

if [ -z "$1" ] ; then
    target=merged-prod
else
    target="$1"
fi
if [ "$target" = "${u}PROD${u}" ] ; then
  echo "PROD is not configured"
  exit 1
fi
if [ "$url" = "${u}URL${u}" ] ; then
  echo "URL is not configured"
  exit 1
fi

function clean_cache {
  php <<EOF
<?php
include_once('app/Mage.php');
Mage::app();
echo "Clean cache\n";
Mage::app()->cleanCache();
EOF
}
function end {
  clean_cache
  newget=`mktemp`
  cp $prod/scripts/get_icrc_mods.sh $newget
  eval "sed -i 's@${u}URL${u}@$url@' $newget"
  eval "sed -i 's@${u}PROD${u}@$prod@' $newget"
  cat $newget > $me ; exit 0
}

cd $prod || exit 1
git fetch || exit 1
git checkout $target || exit 1
if [ "$tag" = "0" ] ; then
    local_branch=$(git rev-parse --symbolic-full-name --abbrev-ref HEAD)
    remote_branch=$(git rev-parse --abbrev-ref --symbolic-full-name @{u})
    set -x
    git merge --ff-only $remote_branch || \
        git rebase --preserve-merges $remote_branch
    set +x
fi
echo "Fix permissions"
chown www-data $prod/var
chown icrc $prod/var/datastudio
find $prod/media -type d -exec chown www-data {} \;
find $prod/sitemap -type d -exec chown www-data {} \;
if [ "x$2" = "x-slave" ] ; then
  end
fi
echo "GET $url"
clean_cache
curl -o /dev/null -k -s "$url"
clean_cache
rm -f $prod/var/locks/*.lock
echo "Re-index all"
php shell/indexer.php reindexall
rm -f $prod/var/locks/*.lock
end
