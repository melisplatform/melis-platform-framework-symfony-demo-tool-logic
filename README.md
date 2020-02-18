# melis-platform-framework-symfony-demo-tool-logic
This bundle handles the request of melisplatform/melis-platform-framework-symfony-demo-tool
to display data. It accesses the database, translates the text and renders the views.

# Getting  Started
These instructions will get you a copy of the project up and running on your machine.

## Prerequisites
You will need the following in order to have this module running:
* melisplatform/melis-platform-framework-symfony

It will automatically be done when using composer.

## Installing
Run the composer command:
```
composer require melisplatform/melis-platform-framework-symfony-demo-tool-logic
```

## Running the code

### Activating the bundle
Activating this bundle is just the same way you activate your symfony bundle inside Symfony application. 
You just need to include its bundle class to the list of bundles inside Symfony application (most probably in bundles.php file).

```
return [
    //All of the symfony activated bundles here
    Symfony\Bundle\FrameworkBundle\FrameworkBundle::class => ['all' => true],
    ...
    ...
    etc.
    //Melis Platform Custom Bundles
    MelisPlatformFrameworkSymfony\MelisPlatformFrameworkSymfonyBundle::class => ['all' => true],
    MelisPlatformFrameworkSymfonyDemoToolLogic\MelisPlatformFrameworkSymfonyDemoToolLogicBundle::class => ['all' => true]
];
```
Don't forget to activate also the MelisPlatformFrameworkSymfonyBundle class since this bundle requires it.

## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-platform-framework-symfony-demo-tool-logic/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License - see the [LICENSE](LICENSE)
