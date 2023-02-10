# modal-php
An easy way for PHP Applications to embed Dialog Windows - Powered by [micromodal.js](https://github.com/ghosh/Micromodal) and [twig](https://twig.symphony.com)

## Usage

#### Requirements
- [Composer](https://getcomposer.org/)
- [Docker or similar (for development)](https://www.docker.com/)
- [phpDocumentor](https://docs.phpdoc.org/)

#### Setup
Add the following to your `composer.json` file:

```json
"repositories": [
    {
        "type": "vcs",
        "url": "https://git.cyber.psych0si.is/modal-php"
    }
]
```

And run `composer require modal-php` in your project folder

#### Code
[Example PHP Usage](index.php)

### Available Channels
An open dialog can send information back to the calling function in two different ways:
- by resolving the returned promise [1](#-using-channel-1-)
- by calling the supplied callback function [2](#-using-channel-2-)

Technically there is also a third way [3](#channel-3), by using the dataset way micromodal.js provides. A shortcut function is available with the `Modal#getOpenButton($label);` function that returns the button as string. This discards the input data and can be used for simple info dialogs.
You can override this behaivor by adding `data-modal-ignore` to the input. Every Input-Element with this attribute gets also skipped in the conversion to an object.

The modal-Promise will always reject if the user cancels the modal. You either have to catch it with `Promise.catch()` or let it silently fail.

#### Using Channel 1

Every Modal returns a Promise that is resolved when the user clicks on Submit. It automatically maps the contained form inputs to a new object based on the name of the element, then closes and resets the form.

```js
//JavaScript
openModalById("channel1-example")
.then(e => {
    console.log(e);
});
```

#### Using Channel 2
If you want to use the modal to control things on the document and require it to stay open, you can add the parameter `data-modal-event` so the modal doesn't resolve and instead streams the data to the callback. If you need to multiplex the stream, you can use parameter `data-event-name` to add a assignable key to the resulting object.

```js
//JavaScript
openModalById("channel2-example", (event) => {
    console.log(event);
});
```

#### Tabs
To add a Tabbed View to your modal add a TabView to your modal, omitting the filename argument of the modal. Add the required templates to the TabView and add it to your modal.

```php
//PHP
$tabModal = new Modal("example4");
$tabs = new TabView();

//setting the third argument to true makes it preselected
$tabs->addTemplate("Tab 1", "tabs/tab1", true);
$tabs->addTemplate("Tab 2", "tabs/tab2");
$tabs->addTemplate("Tab 3", "tabs/tab3");
$tabModal->addTabView($tabs);
```

modal-php will always return all inputs regardless of the currently selected tab

#### Adding data to modals
To add data to the modals, add them as map to the modal. You can use the [Full twig Syntax](https://twig.symfony.com/doc/3.x/templates.html) to access the data inside the modal. This also creates the possibility for reusable components.

```php
//PHP
$m = new Modal("example", "example-file");
$m->setData([
    "welcomestring" => "Welcome to modal-php v0.0.1!"
]);
```

#### Setting templatepath
You can customize the absolute path where the library should search for modal-templates.
```php
//PHP
//Sets template path to ./example
TemplateLoader::setModalPath(__DIR__ . "/example");
```

### Documentation
You can generate a documentation by running `composer docs`. 