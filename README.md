# studies-project-app

### **LEXIK BUNDLE**
**1.** _W tym momencie paczka lexik-jwt-authentication jest zainstalowana i skonfigurowana_

**2.** _Nie dodaje jej konfiguracji do `security.yaml` z racji tego, iż nie wiem jak otrzymać dokumentację open-api i zgrać ją ze swagger-em_

**3.** _Trzeba będzie ją odpalić jak już wszystko będzie śmigać, albo jak dowiem się jak zgrać ją z dokumentacją open-api._

**4.** _Dla zainteresowanych:_: [Lexik Bundle Link](https://github.com/lexik/LexikJWTAuthenticationBundle/blob/2.x/Resources/doc/index.md#installation)

### **UNIT TESTS**
**1.** Przed wykonaniem testów należy pamiętać o utworzeniu bazy testowej:

`php bin/console doctrine:database:create --env=test`

**2.** _Po utworzeniu testowej bazy trzeba pamiętać o uaktualnieniu jej migracjami:_

`php bin/console doctrine:migrations:migrate -n --env=test`

**3.** _Testy wykonuje sie komendą `php bin/phpunit`_

**4.** _Dla zainteresowanych:_: [Info dot. testów w symfony](https://symfony.com/doc/current/the-fast-track/en/17-tests.html)

### **ALICE BUNDLE**

**1.** Alice używa providerów zawartych w [Faker](https://github.com/fzaninotto/Faker#formatters) (typy danych dla 'dummy' plików .yaml pod linkiem)

**2.** Aby zapełnić bazę sztucnzymi danymi należy użyć komendy:

`php bin/console hautelook:fixtures:load --env=test`

**3** _Dla zainteresowanych link do paczki Alice _: [Link do repo i instrukcji alice bundle](https://github.com/nelmio/alice/blob/master/doc/getting-started.md#basic-usage)

#  .......... **UWAGI** ...........

1. Pamiętać, że aktualnie api zwraca po jednym resource na odpowiedz.\
    Testy również są napisane pod jeden resource zwracany w odpowiedzi.\
    Należy pamiętać, aby w razie zmiany liczby resourców zmienić również zawartość testów.\
    Najlepiej będzie to zroibć poprzez wysłanie zapytania `POSTMAN-em` i skopiowanie odpowiedzi do testu.

   
Zrobione Encje : 
Category, \
SubCategory,\
Intake,\
Product -- do dokonczenia \
..



Security tutors:\
[api_platform access_control](https://symfonycasts.com/screencast/api-platform-security/acl-cheese-owner) `{"security"="is_granted('ROLE_USER')"}` \
[api platform onlu owner adding resource](https://symfonycasts.com/screencast/api-platform-security/acl-cheese-owner) `{"security"="is_granted('ROLE_USER') and object.getOwner() == user"}` \
[roznica pomiedzy access_control i security](https://symfonycasts.com/screencast/api-platform-security/previous-object) \
[jezeli logika security dla entity bedzie bardziej skomplikowana](https://symfonycasts.com/screencast/api-platform-security/access-control-voter#play)


**Ważna część API, aby otrzymać poprawną dokumentację należy wysłać zapytanie pod:**

`/api/docs?format=json` ,

`/api/products?format=json` itd.

TO DO: 

`security.yml`: 
obecnie trwają prace nad ustawieniem firewall-a tak zeby przepuszczal zapytania /api do `main-a` jezeli uzytkownik korzysta z przegladarki.
Jezeli header requestu będzie brzmiał `application/json` należy założyć, że nie jest to przeglądarka i skierować użytkownika na entry point `api`



