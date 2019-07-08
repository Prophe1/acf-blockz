# ACF Blocks
This updated version of https://github.com/ItinerisLtd/acf-gutenblocks

## Requirements

<ul>
<li>WordPress >= 4.7</li>
<li>PHP >= 7.1.3 (with php-mbstring enabled)</li>
<li>Composer</li>
</ul>

## Installation


## Usage

1. Activate the plugin
2. Create a directory to store your Blocks in your plugin or theme
3. Define your [Block](#block-definition) and frontend template

  ```
  Blocks/
    └── Button.php
  ```

4. Register your Block by appending the Block class name as a string to the `acf_gutenblocks/blocks` filter

```php
add_filter('acfblocks/blocks', function (array $blocks): array {
    $new_blocks = [
        Button::class,
    ];
    return array_merge($blocks, $new_blocks);
});
```

## Block definition

Blocks are registered using PHP classes to provide a simple "Controller" to allow separation of logic and functionality from your template. This can really help to isolate and organise code that is intended only for that Block.

To create a Block, you must extend your class from the available Block constructors and pass any valid [`acf_register_block()`](https://www.advancedcustomfields.com/resources/acf_register_block/) arguments to the parent constructor. Here can also define your controller methods for use within your template.

```php
# App/Blocks/Button.php
<?php

declare(strict_types=1);

namespace App\Blocks;

use Prophe1\ACFBlockz\AbstractBlock;

class Button extends AbstractBlock
{
    public function __construct()
    {
        parent::__construct([
            'title' => __('Button', 'fabric'),
            'description' => __('Button description', 'fabric'),
            'category' => 'formatting',
            // Other valid acf_register_block() settings
        ]);
    }

    public function getItems(): array
    {
        $items = [];
        foreach (get_field('list_items') as $item) {
            if ($item['enabled']) {
                $items[] = $item['list_item'];
            }
        }
        return $items;
    }
}
```

### Block constructors

#### `AbstractBlock`

Extend from this class to register a vanilla PHP template.

#### `AbstractBladeBlock`

If your project uses the [Sage](https://roots.io/sage) theme, you can take advantage of Blade templating by extending from this class (in future, [Sage](https://roots.io/sage) will be optional).

## Controller

Your Block constructor class is available to your template via `$controller`. This allows you to create truly advanced Blocks by organising all of your functional code and logic into a place where you can take more advantage of an OOP approach.

In the [Block definition](#block-definition) example in this page, we have the `getItems` method which can be used in the template like so:

```php
# recources/views/blocks/button.blade.php
<?php foreach ($controller->getItems() as $item) : ?>
    <p><?php echo $item['title']; ?></p>
<?php endforeach; ?>
```

## Fields

You can define your ACF fields in your Block by returning an array of fields in the `registerFields` method.

### Simple array

Read more [here](https://www.advancedcustomfields.com/resources/register-fields-via-php/#example).

```php
protected function registerFields(): array
{
    return [
        // Any valid field settings
    ];
}
```

### ACF Builder

```php
protected function registerFields(): array
{
    $testimonial = new FieldsBuilder('testimonial');

    $testimonial
        ->setLocation('block', '==', 'acf/testimonial');

    $testimonial
        ->addText('quote')
        ->addText('cite')
        ->addRepeater('list_items')
            ->addText('list_item')
            ->addTrueFalse('enabled', [
                'ui' => 1,
                'default_value' => 1,
            ])
        ->endRepeater();

    return $testimonial->build();
}
```