# melis-platform-framework-symfony-demo-tool-logic
This bundle handle the request of melisplatform/melis-platform-framework-symfony-demo-tool
to display data. It access the database, translates the text and rendered the views.

# Getting  Started
This instructions will get you a copy of the project up and running on your machine.

## Prerequisites
You will need the following in order to have this module running.
* melisplatform/melis-platform-framework-symfony

This will automatically be done when using composer.

## Installing
Run the composer command:
```
composer require melisplatform/melis-platform-framework-symfony-demo-tool-logic
```

## Running the code

### Activating the bundle
Activating this bundle is just the same 
the way you activate your symfony bundle inside symfony application. 
You just need to include it's bundle class to the list of bundle inside 
symfony application most probably in bundles.php file.

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
Don't forget to activate also the MelisPlatformFrameworkSymfonyBundle class since this bundle require's it.

### Routes
Since this bundle has it's own routes, we need to include it inside symfony application
so that symfony can recognize it.\
So inside symfony application, most likely in routes.yaml file (if symfony uses yaml as extension)
we just need to include the bundle's route.
```
melis_platform:
  resource: "@MelisPlatformFrameworkSymfonyDemoToolLogicBundle/Resources/config/routing.yaml"
  prefix:   /
``` 

## Authors

* **Melis Technology** - [www.melistechnology.com](https://www.melistechnology.com/)

See also the list of [contributors](https://github.com/melisplatform/melis-platform-framework-symfony-demo-tool-logic/contributors) who participated in this project.


## License

This project is licensed under the OSL-3.0 License - see the [LICENSE](LICENSE)