
INSTALLATION
============

### SYSTEM REQUIREMENTS

* A working Bareos environment, Bareos >= 12.4
* An Apache 2.x Webserver with mod-rewrite, mod-php5 and mod-setenv
* OR an nginx + php-fpm (tested on 1.1.19 + PHP 5.5.23 on ubuntu)
* PHP >= 5.3.3
    * PHP PDO Extension
    * PHP Sockets Extension
    * PHP OpenSSL Extension


Make sure to set max_memory or you will run into problems in the director view
memory_limit = 512M

* Zend Framework 2.2.x or later

  **Note:** Unfortunately, not all distributions offer a Zend Framework 2 package.
  The following list shows where to get the Zend Framework 2 package.

  * RHEL, CentOS
    * https://fedoraproject.org/wiki/EPEL
    * https://apps.fedoraproject.org/packages/php-ZendFramework2

  * Fedora
    * https://apps.fedoraproject.org/packages/php-ZendFramework2

  * SUSE, Debian, Ubuntu
    * http://download.bareos.org/bareos/contrib

### PACKAGE BASED INSTALLATION

Bareos-WebUI packages are available for a number of Linux distributions, see [Bareos contrib](http://download.bareos.org/bareos/contrib/) repository.

#### Step 1 - Adding the Repository

Add the [Bareos contrib](http://download.bareos.org/bareos/contrib/) repository that is matching your Linux distribution and install the bareos-webui package via your package manager.

* RHEL, CentOS and Fedora

```
#
# define parameter
#

DIST=CentOS_7
# or
# DIST=RHEL_6
# DIST=RHEL_7
# DIST=Fedora_20
# DIST=Fedora_21
# DIST=CentOS_6

# add the Bareos contrib repository
URL=http://download.bareos.org/bareos/contrib/$DIST
wget -O /etc/yum.repos.d/bareos:contrib.repo $URL/bareos:contrib.repo

# install bareos-webui package
yum install bareos-webui
```

* SUSE Linux Enterprise Server (SLES), openSUSE

```
#
# define parameter
#

DIST=SLE_12
# or
# DIST=SLE_11_SP3
# DIST=openSUSE_13.1
# DIST=openSUSE_13.2

# add the Bareos contrib repository
URL=http://download.bareos.org/bareos/contrib/$DIST
zypper addrepo --refresh $URL/bareos:contrib.repo

# install bareos-webui package
zypper install bareos-webui
```

* Debian, Ubuntu

```
#
# define parameter
#

DIST=Debian_7.0
# or
# DIST=Debian_6.0
# DIST=xUbuntu_12.04
# DIST=xUbuntu_14.04

# add the Bareos contrib repository
URL=http://download.bareos.org/bareos/contrib/$DIST
printf "deb $URL /\n" >> /etc/apt/sources.list.d/bareos_contrib.list

# add package key
wget -q $URL/Release.key -O- | apt-key add -

# install bareos-webui package
apt-get update
apt-get install bareos-webui

```

#### Step 2 - Configuration of a restricted console

You can have multiple Consoles with different names and passwords, sort of like multiple users, each with different privileges.
As a default, these consoles can do absolutely nothing – no commands whatsoever. You give them privileges or rather access to
commands and resources by specifying access control lists in the Director’s Console resource. The ACLs are specified by a directive
followed by a list of access names.

It is required to add at least one restricted named console in your director configuration (bareos-dir.conf) for the webui.
The restricted named consoles, configured in your bareos-dir.conf, are used for authentication and access control. The name
and password directives of the restricted consoles are the credentials you have to provide during authentication to the webui
as username and password. For full access and functionality relating the director connection the following commands are
currently needed by the webui and have to be made available via the CommandACL in your restricted consoles.

* status
* messages
* show
* version
* run
* rerun
* cancel

The package install provides a default configuration under /etc/bareos/bareos-dir.d/bareos-webui.conf, which has to be included
at the bottum of your /etc/bareos/bareos-dir.conf and edited to your needs.

```
echo "@/etc/bareos/bareos-dir.d/bareos-webui.conf" >> /etc/bareos/bareos-dir.conf
```

**Note:** Most parts of the webui still use a direct connection to the catalog database to retrieve data, so the configured ACL
of your restricted consoles do not work here! For the moment it is more or less just used for authentication and you should
or may be fine with the defaults provided in the example below. However, in future direct connection to the catalog database
will be droped and fully replaced by the native connection to the director itself.

```
#
# Restricted console used by bareos-webui
#
Console {
  Name = user1
  Password = "password"
  CommandACL = status, messages, show, version, run, rerun, cancel
  Job ACL = *all*
  Schedule ACL = *all*
  Catalog ACL = *all*
  Pool ACL = *all*
  Storage ACL = *all*
  Client ACL = *all*
  FileSet ACL = *all*
  #Where ACL =
}
```
For more details about console resource configuration in bareos, please have a look at the online [Bareos documentation](http://doc.bareos.org/).

**Note:** Do not forget to reload your new director configuration.

#### Step 3 - Configure the catalog database connection

Bareos-WebUI needs just a read-only connection to the Bareos catalog database, so there are multiple possibilities to configure bareos-webui.
You may reuse the exsiting Bareos database user or create a new (read-only) database user for bareos-webui. The first approach is simpler to
configure, the second approach is more secure.

##### PostgreSQL - Create a read-only catalog database user

In this example we use *bareos_webui* as database username. As database password *<DATABASE_PASSWORD>*,
please choose a random password.

**Note:** PostgreSQL user names are not allowed to contain -.

Since Bareos >= 14.1 you are able to do it the following way:

```
su - postgres

DB_USER=bareos_webui

DB_PASS=<DATABASE_PASSWORD>

/usr/lib/bareos/scripts/bareos-config get_database_grant_priviliges postgresql $DB_USER $DB_PASS readonly > /tmp/database_grant_priviliges.sql

psql -d bareos -f /tmp/database_grant_priviliges.sql

rm /tmp/database_grant_priviliges.sql

```

Add the following lines before the "all" rules into your *pg_hba.conf*.
Usually found under /etc/postgres/*/main/pg_hba.conf or /var/lib/pgsql/data/pg_hba.conf.

```
# TYPE	DATABASE	USER		ADDRESS                 METHOD
host	bareos		bareos_webui    127.0.0.1/32		md5
host    bareos		bareos_webui    ::1/128			md5
```

Finally, reload your PostgreSQL configuration or restart your PostgreSQL Server (```pg_ctl reload``` or ```service postgresql restart```).

##### MySQL - Create a read-only catalog database user

In this example we use *bareos_webui* as database username. As database password *<DATABASE_PASSWORD>*,
please choose a secure random password.

```
DB_USER=bareos_webui

DB_PASS=<DATABASE_PASSWORD>

/usr/lib/bareos/scripts/bareos-config get_database_grant_priviliges mysql $DB_USER $DB_PASS readonly > /tmp/database_grant_privileges.sql

mysql < /tmp/database_grant_privileges.sql

mysqladmin flush-privileges

rm /tmp/database_grant_privileges.sql
```

#### Step 4 - Configure your Apache Webserver

If you have installed from package, a default configuration is provided, please see /etc/apache2/conf.d/bareos-webui.conf,
/etc/httpd/conf.d/bareos-webui.conf or /etc/apache2/available-conf/bareos-webui.conf.

Required apache modules, setenv, rewrite and php are enabled via package postinstall script.
You simply need to restart your apache webserver manually.

#### Step 4 alternative - Configure your nginx Webserver

If you have installed from package, a default configuration is provided, please see /etc/nginx/sites-available/default, on ubuntu,
put the provided configuration file from the package (see install/nginx/bareos-webui.conf) into /etc/nginx/sites-available/ and 
symlink this file from /etc/nginx/sites-enabled:

make the needed edits (change the port too!)

```
cd /etc/nginx/sites-enabled
ln -s /etc/nginx/sites-available/bareos-webui.conf .
```

No additional modules are needed for nginx, but you do have to install php5-fpm.  You will want the latest 5.5 PHP , so if you
are on ubuntu 12.04 LTS, that means installing php fpm from PPA.  I recommend this:

```
sudo add-apt-repository ppa:ondrej/php5
sudo apt-get update
sudo apt-get upgrade
sudo apt-get install php5 php5-fpm
```

Make sure you have a php5-fpm pool in /etc/php5/fpm/pool.d/www.conf
 
Then you simply need to restart your nginx webserver and php5-fpm manually.

```
service nginx restart 
service php5-fpm restart
```

or old school as long as it works :

```
/etc/init.d/nginx restart
/etc/init.d/php5-fpm restart
```

#### Step 5 - Configure your /etc/bareos-webui/directors.ini

Configure your database and director connections in */etc/bareos-webui/directors.ini* to match your database and director settings,
which you have choosen in the previous steps.

The configuration file /etc/bareos-webui/directors.ini should look similar to this:

```
;
; Bareos WebUI Configuration
; File: /etc/bareos-webui/directors.ini
;

;
; Section localhost-dir
;
[localhost-dir]

; Enable or disable section. Possible values are "yes" or "no", the default is "yes".
enabled = "yes"

; Possible values are "postgresql" or "mysql", the default is "postgresql"
dbdriver = "postgresql"

; Fill in the IP-Address or FQDN of your catalog DB
dbaddress = "localhost"

; Default values are corresponding to your dbriver, e.g 5432 or 3306
dbport = 5432

; Default is "bareos"
dbuser = "bareos_webui"

; No default value
dbpassword = ""

; Default is set to "bareos"
dbname = "bareos"

; Fill in the IP-Address or FQDN of you director.
diraddress = "localhost"

; Default value is 9101
dirport = 9101

; Note: TLS has not been tested and documented, yet.
;tls_verify_peer = false
;server_can_do_tls = false
;server_requires_tls = false
;client_can_do_tls = false
;client_requires_tls = false
;ca_file = ""
;cert_file = ""
;cert_file_passphrase = ""
;allowed_cns = ""

;
; Section remote-dir
;
[remote-dir]
enabled = "no"
dbdriver = "mysql"
dbaddress = "hostname"
dbport = 5432
dbuser = "bareos_webui"
dbpassword = ""
dbname = "bareos"
diraddress = "hostname"
dirport = 9101
; Note: TLS has not been tested and documented, yet.
;tls_verify_peer = false
;server_can_do_tls = false
;server_requires_tls = false
;client_can_do_tls = false
;client_requires_tls = false
;ca_file = ""
;cert_file = ""
;cert_file_passphrase = ""
;allowed_cns = ""

```

**Note:** You can add as many sections (director and catalog combinations) as you want.

#### Step 6 - SELinux

If you do not use SELinux on your system, you can skip this step and go over to the next one.

To install bareos-webui on a system with SELinux enabled, the following additional steps must be performed.

 * Allow HTTPD scripts and modules to connect to the network

```
setsebool -P httpd_can_network_connect on
```

 * Allow HTTPD to connect to a remote database

```
setsebool -P httpd_can_network_connect_db on
```

#### Step 7 - Test your configuration

Finally, you can test if bareos-webui is configured properly by using the url below.

```
http://<replace-with-your-hostname>/bareos-webui/install/test
```

If everything is fine you are able to login, now.

```
http://<replace-with-your-hostname>/bareos-webui/
```

