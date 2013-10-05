Exec { path => [ "/bin/", "/sbin/" , "/usr/bin/", "/usr/sbin/" ] }

define line($file, $line, $ensure = 'present') {
    case $ensure {
        default : { err ( "unknown ensure value ${ensure}" ) }
        present: {
            exec { "/bin/echo '${line}' >> '${file}'":
                unless => "/bin/grep -qFx '${line}' '${file}'"
            }
        }
        absent: {
            exec { "/bin/grep -vFx '${line}' '${file}' | /usr/bin/tee '${file}' > /dev/null 2>&1":
              onlyif => "/bin/grep -qFx '${line}' '${file}'"
            }

            # Use this resource instead if your platform's grep doesn't support -vFx;
            # note that this command has been known to have problems with lines containing quotes.
            # exec { "/usr/bin/perl -ni -e 'print unless /^\\Q${line}\\E\$/' '${file}'":
            #     onlyif => "/bin/grep -qFx '${line}' '${file}'"
            # }
        }
    }
}

class repos {
    file { "/etc/apt/sources.list.d/dotdeb.list":
      ensure => file,
      owner => root,
      group => root,
      source => "/vagrant/puppet/conf/apt/dotdeb.list",
    }
    exec { "import-gpg":
      command => "/usr/bin/wget -q http://www.dotdeb.org/dotdeb.gpg -O -| /usr/bin/apt-key add -"
    }

    exec { "/usr/bin/apt-get update":
      require => [File["/etc/apt/sources.list.d/dotdeb.list"], Exec["import-gpg"]],
    }
}

class mysql {
  package { "mysql-server":
    ensure => installed,
    require => Class['repos'],
  }

  service { 'mysql':
      ensure => 'running',
      enable => true,
      hasrestart => true,
      hasstatus => true,
      subscribe => Package['mysql-server'],
  }

  exec { "set-mysql-password":
    unless  => "mysql -uroot -pvagrant",
    path    => ["/bin", "/usr/bin"],
    command => "mysqladmin -uroot password vagrant",
    require => Service["mysql"],
  }

  # Equivalent to /usr/bin/mysql_secure_installation without providing or setting a password
  exec { 'mysql_secure_installation':
      command => '/usr/bin/mysql -uroot -pvagrant -e "DELETE FROM mysql.user WHERE User=\'\'; DROP DATABASE IF EXISTS test; FLUSH PRIVILEGES;" mysql',
      require => Exec['set-mysql-password'],
  }

  exec { "create-database":
    unless  => "/usr/bin/mysql -uroot -pvagrant main",
    command => "/usr/bin/mysql -uroot -pvagrant -e \"CREATE DATABASE IF NOT EXISTS main;\"",
    require => Exec["set-mysql-password"],
  }
}

class mod-php {
  package { 'php5':
    ensure => 'present',
    require => Class['repos'],
    notify => Service['apache2'],
  }

  file {'/etc/php5/conf.d/timezone.ini':
      owner  => root,
      group  => root,
      mode   => 664,
      source => "/vagrant/puppet/conf/php/timezone.ini",
      require => Package['php5'],
      notify => Service['apache2'],
  }

  package { [
          'php5-mysql',
          'php5-gd',
          'php5-xdebug',
          'php5-apc',
          'php5-mcrypt',
          'php5-cli',
          'php5-dev',
          'php5-curl',
          'php5-intl',
      ]:
      ensure => 'present',
      require => Package['php5'],
      notify => Service['apache2'],
  }
}

class httpd {
    package {'apache2':
        ensure => 'present'
    }

    service {'apache2':
        ensure => 'running',
        require => Package['apache2'],
    }

    exec { "sed -i 's/www-data/vagrant/g' /etc/apache2/envvars && rm -rf /var/lock/apache2":
        notify => Service['apache2'],
        require => Package['apache2'],
    }

    file {'/etc/apache2/sites-enabled/vhost.conf':
        owner  => root,
        group  => root,
        mode   => 664,
        source => "/vagrant/puppet/conf/httpd/vhost.conf",
        notify => Service['apache2'],
        require => Package['apache2'],
    }

    file {'/etc/apache2/conf.d/enablesendfile.conf':
        owner  => root,
        group  => root,
        mode   => 664,
        source => "/vagrant/puppet/conf/httpd/enablesendfile.conf",
        notify => Service['apache2'],
        require => Package['apache2'],
    }

    exec { 'enable rewrite':
      creates => '/etc/apache2/mods-enabled/rewrite.load',
      command => '/usr/sbin/a2enmod rewrite',
      notify => Service['apache2'],
      require => Package['apache2'],
    }
}

class pear {
  package {'php-pear':
    ensure => present,
    require => Class['mod-php'],
  }

  exec {'pear upgrade':
    command => '/usr/bin/pear upgrade',
    require => Package['php-pear'],
    returns => [0, '', ' ']
  }

  exec {'pear auto_discover':
    command => '/usr/bin/pear config-set auto_discover 1',
    require => Package['php-pear'],
  }
}

class bootstrap-composer {
  package {'git':
      ensure => present
  }

  exec { 'install-composer':
      command => "/bin/sh -c 'cd /vagrant && /usr/bin/curl -sS https://getcomposer.org/installer | /usr/bin/php'",
      require => [Class['mod-php']],
  }

  exec { 'update-composer':
      command => "/bin/sh -c 'export COMPOSER_HOME=/home/vagrant && export COMPOSER_PROCESS_TIMEOUT=4000 && cd /vagrant && /usr/bin/php composer.phar install --prefer-dist'",
      require => [Exec['install-composer'], Package['git']],
      timeout => 0,
      tries => 10,
  }

  exec { 'update-db':
        command => "/bin/sh -c 'cd /vagrant && SG_ENV=local bin/ruckus.php db:migrate'",
        require => [Exec['update-composer'], Exec['create-database']],
    }
}

include repos
include mysql
include httpd
include mod-php
include pear
include bootstrap-composer