# studies-project-app

### **LEXIK BUNDLE**
**1.** _W tym momencie paczka lexik-jwt-authentication jest zainstalowana i skonfigurowana_

**2.** _Nie dodaje jej konfiguracji do `security.yaml` z racji tego, iż nie wiem jak otrzymać dokumentację open-api i zgrać ją ze swagger-em_

**3.** _Trzeba edzie ją odpalić jak już wszystko będzie śmigać, albo jak dowiem się jak zgrać ją z dokumentacją open-api._

### **UNIT TESTS**
**1.** Przed wykonaniem testów należy pamiętać o utworzeniu bazy testowej:

`php bin/console doctrine:database:create --env=test`

**2.** _Po utworzeniu testowej bazy trzeba pamiętać o uaktualnieniu jej migracjami:_

`php bin/console doctrine:migrations:migrate -n --env=test`

