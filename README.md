#### Installation:
1. Install using composer:
    ```bash
    composer require nedac/sylius-minimum-order-value-plugin
    ```

2. Add to bundles.php:
    ```php
    # config/bundles.php
    <?php

    return [
        # ...
        Nedac\SyliusMinimumOrderValuePlugin\NedacSyliusMinimumOrderValuePlugin::class => ['all' => true],
    ];
    ```

3. Implement the interface and use the trait in the Channel model:
    ```php
    # src/entity/Channel/Channel.php
    <?php

    declare(strict_types=1);

    namespace Tests\Nedac\SyliusMinimumOrderValuePlugin\Application\Entity\Channel;

    use Doctrine\ORM\Mapping as ORM;
    use Nedac\SyliusMinimumOrderValuePlugin\Model\MinimumOrderValueAwareInterface;
    use Nedac\SyliusMinimumOrderValuePlugin\Model\MinimumOrderValueTrait;
    use Sylius\Component\Core\Model\Channel as BaseChannel;

    /**
     * @ORM\Entity
     * @ORM\Table(name="sylius_channel")
     */
    class Channel extends BaseChannel implements MinimumOrderValueAwareInterface
    {
        use MinimumOrderValueTrait;
        # ...
    }
    ```

4. Generate and run database migration:
    ```bash
    bin/console doctrine:migrations:diff
    bin/console doctrine:migrations:migrate
    ```

#### Setup development environment:
```bash
docker-compose build
docker-compose up -d
docker-compose exec php composer --working-dir=/srv/sylius install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application install
docker-compose run --rm nodejs yarn --cwd=/srv/sylius/tests/Application build
docker-compose exec php bin/console assets:install public
docker-compose exec php bin/console doctrine:schema:create
docker-compose exec php bin/console sylius:fixtures:load -n
```
#### Running tests:
```bash
docker-compose exec php sh
cd ../..
vendor/bin/behat
```
