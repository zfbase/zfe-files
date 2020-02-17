# Единая точка загрузки и управления файлами для приложений на ZFE

В настоящий момент для использования zfe-files необходимо включения React в проект.


## Установка
```bash
composer require zfbase/zfe-files
npm install zfbase/zfe-files --save
```


## Подключение

### 1.Добавить модель для файлов
Рекомендованная схема модели: `assets/schema/Files.yml`.

Модель должна реализовывать интерфейс `ZfeFiles_FileInterface`.

Название модели для хранения файлов тоже может быть любым. Их даже может быть несколько.

`application/models/Files.php`
```php
class Files extends BaseFiles implements ZfeFiles_FileInterface
{
    public function getPathHelper(): ZfeFiles_PathHelper_Abstract
    {
        return new ZfeFiles_PathHelper_Default($this);
    }

    public function getDataForUploader(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->title,
            'previewUrl' => $this->getPreviewUrl(),
            'downloadUrl' => $this->getDownloadUrl(),
        ];
    }

    public function getPreviewUrl(): string
    {
        return '/' . static::getControllerName() . '/preview/id/' . $this->id;
    }

    public function getDownloadUrl(): string
    {
        return '/' . static::getControllerName() . '/download/id/' . $this->id;
    }

    public static function getUploadUrl(): string
    {
        return '/' . static::getControllerName() . '/upload';
    }
}
```


### 2. Добавить контроллер для управления файлами

Контроллер может быть любым. Их тоже может быть несколько.

`application/controllers/FilesController.php`
```php
class FilesController extends ZfeFiles_Controller_DefaultAjax
{
    protected static $_modelName = Files::class;
}
```


### 3. Разрешить доступ к контроллеру для управления файлами

`application/configs/acl.ini`
```ini
acl.resources.allow.files.all = user
```


### 4. Рекомендуется указать настройки по умолчанию

`application/configs/application.ini`
```ini
files.modelName = "Files"
files.uploader = "ZfeFiles_Uploader_DefaultAjax"
files.uploadHandler = "ZfeFiles_Uploader_Handler_Simple"
files.root = DATA_PATH "/files"

webserver = 'nginx'
```


### 5. Подключить расширения форм

Заменить в `Application_Form_Helpers` трейт `ZFE_Form_Helpers` на `ZfeFiles_Form_Helpers`.

`application/forms/Helpers.php`
```php
 trait Application_Form_Helpers
 {
    use ZfeFiles_Form_Helpers;
 }
```


### 6. Добавить поддержку помошников представления для элементов форм

`application/Bootstrap.php`
```php
class Application_Bootstrap extends ZFE_Bootstrap
{
    /**
     * @inheritDoc
     */
    protected function _initLayout()
    {
        parent::_initLayout();

        $layout = Zend_Layout::getMvcInstance();
        $view = $layout->getView();
        $view->addHelperPath(
            APPLICATION_PATH . '/../vendor/zfbase/zfe-files/library/ZfeFiles/View/Helper',
            'ZfeFiles_View_Helper_');
    }
}
```

### 7. Подключить компонент в JavaScript
`assets/sources/app.js`
```js
import { createFileAjax } from 'zfe-files';

// ...

App.init({
  initialMethods: [...App.initialMethods, 'initFileAjax'],
  initFileAjax: (container) => {
    $('.zfe-files-ajax', container).each((i, el) => createFileAjax(el));
  },
});
```

### 8. Подключить стили
`assets/sources/app.scss`
```scss
@import 'zfe-files/src/index.scss';
```

## Использование
На примере подключения к статьям.

### Добавить в модель схему использования файлов.
Использующая файлы модель должна реализовывать интерфейс `ZfeFiles_Manageable`, позволяющий прикреплять файлы и управлять ими.

`application/models/Articles.php`
```php
class Articles extends BaseArticles implements ZfeFiles_Manageable
{
    protected static $fileSchemas;
    
    public static function getFileSchemas(): ZfeFiles_Schema_Collection
    {
        if (!static::$fileSchemas) {
            static::$fileSchemas = new ZfeFiles_Schema_Collection();
            static::$fileSchemas->add(new ZfeFiles_Schema_Default([
                'code' => 'file',
                'title' => 'Файл',
            ]));
        }

        return static::$fileSchemas;
    }

    public function toArray($deep = true, $prefixKey = false)
    {
        $array = parent::toArray($deep, $prefixKey);

        foreach (static::getFileSchemas() as $fileSchema) {
            $code = $fileSchema->getCode();
            $array[$code] = [];
            $files = ZfeFiles_Dispatcher::loadFiles($this, $code);
            foreach ($files as $file) {  /** @var ZfeFiles_FileInterface $file */
                $array[$code][] = $file->getDataForUploader();
            }
        }

        return $array;
    }

    public function fromArray(array $array, $deep = true)
    {
        foreach (static::getFileSchemas() as $fileSchema) {
            $code = $fileSchema->getCode();
            if (array_key_exists($code, $array)) {
                ZfeFiles_Dispatcher::updateFiles($this, $code, $array[$code]);
                unset($array[$code]);
            }
        }

        parent::fromArray($array, $deep);
    }
}
```

### Подключить элемент управляения файлами в форму
`application/forms/Edit/Article.php`
```php
class Application_Form_Edit_Article extends ZFE_Form_Edit_AutoGeneration
{
    protected $_modelName = Articles::class;

    public function init()
    {
        parent::init();

        $this->addFileAjaxElement('file');
    }
}
```
