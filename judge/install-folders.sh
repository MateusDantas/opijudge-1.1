#!/bin/bash

git clone "https://github.com/openjudge/sandbox.git"

mkdir "/var/codes-opijudge"
chown -R www-data:www-data "/var/codes-opijudge"
chmod 777 "/var/codes-opijudge"

mkdir "/var/problems-opijudge"
chown -R www-data:www-data "/var/problems-opijudge"
chmod 777 "/var/problems-opijudge"

mkdir "/var/runs-opijudge"
chown -R www-data:www-data "/var/runs-opijudge"
chmod 777 "/var/runs-opijudge"

mkdir "/var/judge-opijudge"
chown -R www-data:www-data "/var/judge-opijudge"
chmod 777 "/var/judge-opijudge"

cp -r * "/var/judge-opijudge"
