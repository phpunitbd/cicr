#!/bin/bash
tmp=`mktemp -d`
if [ -z "$git" ] ; then
  git=git-ro@distrib.data.fr:/var/lib/git/icrc-hosting.git/
fi
if [ -z "$target" ] ; then
    target=master
fi

git archive --remote=$git $target naxsi/output | tar -x -C $tmp || exit 1
diff -u /etc/nginx/learning.rules $tmp/naxsi/output/whitelist

cp -i $tmp/naxsi/output/whitelist /etc/nginx/learning.rules && /etc/init.d/nginx reload

rm -Rf $tmp

