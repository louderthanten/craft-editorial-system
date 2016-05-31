<?php
namespace Craft;

/**
 * Seomatic Meta field type
 */
class Seomatic_MetaFieldType extends BaseFieldType
{

    public function getName()
    {
        return Craft::t('SEOmatic Meta');
    }

    public function defineContentAttribute()
    {
        return AttributeType::Mixed;
    }

    public function getInputHtml($name, $value)
    {
        if (isset($this->element))
        {

        $id = craft()->templates->formatInputId($name);
        $namespacedId = craft()->templates->namespaceInputId($id);

        // Include our Javascript & CSS
        craft()->templates->includeCssResource('seomatic/css/css-reset.css');
        craft()->templates->includeCssResource('seomatic/css/prism.min.css');
        craft()->templates->includeCssResource('seomatic/css/bootstrap-tokenfield.css');
        craft()->templates->includeCssResource('seomatic/css/style.css');
        craft()->templates->includeCssResource('seomatic/css/field.css');
        craft()->templates->includeJsResource('seomatic/js/field.js');
        craft()->templates->includeJsResource('seomatic/js/jquery.bpopup.min.js');
        craft()->templates->includeJsResource('seomatic/js/prism.min.js');
        craft()->templates->includeJsResource('seomatic/js/bootstrap-tokenfield.min.js');

        $variables = array(
            'id' => $id,
            'name' => $name,
            'meta' => $value,
            'element' => $this->element,
            'field' => $this->model,
            );

        $jsonVars = array(
            'id' => $id,
            'name' => $name,
            'namespace' => $namespacedId,
            'prefix' => craft()->templates->namespaceInputId(""),
            );

        if (isset($variables['locale']))
            $locale = $variables['locale'];
        else
            $locale = craft()->language;

        $siteMeta = craft()->seomatic->getSiteMeta($locale);
        $titleLength = craft()->config->get("maxTitleLength", "seomatic");
        if ($siteMeta['siteSeoTitlePlacement'] == "none")
            $variables['titleLength'] = $titleLength;
        else
            $variables['titleLength'] = ($titleLength - strlen(" | ") - strlen($siteMeta['siteSeoName']));

/* -- Prep some parameters */

        // Whether any assets sources exist
        $sources = craft()->assets->findFolders();
        $variables['assetsSourceExists'] = count($sources);

        // URL to create a new assets source
        $variables['newAssetsSourceUrl'] = UrlHelper::getUrl('settings/assets/sources/new');

        // Set asset ID
        $variables['seoImageId'] = $variables['meta']->seoImageId;

        // Set asset elements
        if ($variables['seoImageId']) {
            if (is_array($variables['seoImageId'])) {
                $variables['seoImageId'] = $variables['seoImageId'][0];
            }
            $asset = craft()->elements->getElementById($variables['seoImageId']);
            $variables['elements'] = array($asset);
        } else {
            $variables['elements'] = array();
        }

        // Set element type
        $variables['elementType'] = craft()->elements->getElementType(ElementType::Asset);

        $variables['assetSources'] = $this->getSettings()->assetSources;

        $variables['seoTitleSourceChangeable'] = $this->getSettings()->seoTitleSourceChangeable;
        $variables['seoDescriptionSourceChangeable'] = $this->getSettings()->seoDescriptionSourceChangeable;
        $variables['seoKeywordsSourceChangeable'] = $this->getSettings()->seoKeywordsSourceChangeable;
        $variables['seoImageIdSourceChangeable'] = $this->getSettings()->seoImageIdSourceChangeable;
        $variables['twitterCardTypeChangeable'] = $this->getSettings()->twitterCardTypeChangeable;
        $variables['openGraphTypeChangeable'] = $this->getSettings()->openGraphTypeChangeable;
        $variables['robotsChangeable'] = $this->getSettings()->robotsChangeable;

/* -- Extract a list of the other plain text fields that are in this entry's layout */

        $fieldList = array('title' => 'Title');
        $fieldData = array('title' => $this->element->content['title']);
        $fieldImage = array();
        $imageFieldList = array();
        $fieldLayouts = $this->element->fieldLayout->getFields();
        foreach ($fieldLayouts as $fieldLayout)
        {
            $field = craft()->fields->getFieldById($fieldLayout->fieldId);

            switch ($field->type)
            {
                case "PlainText":
                case "RichText":
                case "RedactorI":
                    $fieldList[$field->handle] = $field->name;
                    $fieldData[$field->handle] = craft()->seomatic->truncateStringOnWord(
                            strip_tags($this->element->content[$field->handle]),
                            200);
                    break;

                case "Matrix":
                    $fieldList[$field->handle] = $field->name;
                    $fieldData[$field->handle] = craft()->seomatic->truncateStringOnWord(
                            craft()->seomatic->extractTextFromMatrix($this->element[$field->handle]),
                            200);
                    break;

                case "Tags":
                    $fieldList[$field->handle] = $field->name;
                    $fieldData[$field->handle] = craft()->seomatic->truncateStringOnWord(
                            craft()->seomatic->extractTextFromTags($this->element[$field->handle]),
                            200);
                    break;

                case "Assets":
                    $imageFieldList[$field->handle] = $field->name;
                    $img = $this->element[$field->handle]->first();
                    if ($img)
                        {
                            $fieldImage[$field->handle] = $img->url;
                        }
                    break;
            }
        }
        $variables['fieldList'] = $fieldList;
        $variables['imageFieldList'] = $imageFieldList;
        $variables['elementId'] = $this->element->id;
        $jsonVars['fieldData'] = $fieldData;
        $jsonVars['fieldImage'] = $fieldImage;
        $jsonVars['missing_image'] = UrlHelper::getResourceUrl('seomatic/images/missing_image.png');
        $jsonVars = json_encode($jsonVars);
        craft()->templates->includeJs("$('#{$namespacedId}').SeomaticFieldType(" . $jsonVars . ");");
        return craft()->templates->render('seomatic/field', $variables);
        }
    }

    /**
     * Define our settings
     * @return none
     */
    protected function defineSettings()
        {
            return array(
                'assetSources' => AttributeType::Mixed,

                'seoTitle' => AttributeType::String,
                'seoTitleSource' => array(AttributeType::String, 'default' => 'field'),
                'seoTitleSourceField' => array(AttributeType::String, 'default' => 'title'),
                'seoTitleSourceChangeable' => array(AttributeType::Bool, 'default' => 1),

                'seoDescription' => AttributeType::String,
                'seoDescriptionSource' => AttributeType::String,
                'seoDescriptionSourceField' => AttributeType::String,
                'seoDescriptionSourceChangeable' => array(AttributeType::Bool, 'default' => 1),

                'seoKeywords' => AttributeType::String,
                'seoKeywordsSource' => AttributeType::String,
                'seoKeywordsSourceField' => AttributeType::String,
                'seoKeywordsSourceChangeable' => array(AttributeType::Bool, 'default' => 1),

                'seoImageIdSource' => AttributeType::String,
                'seoImageIdSourceField' => AttributeType::String,
                'seoImageIdSourceChangeable' => array(AttributeType::Bool, 'default' => 1),

                'twitterCardType' => AttributeType::String,
                'twitterCardTypeChangeable' => array(AttributeType::Bool, 'default' => 1),

                'openGraphType' => AttributeType::String,
                'openGraphTypeChangeable' => array(AttributeType::Bool, 'default' => 1),

                'robots' => AttributeType::String,
                'robotsChangeable' => array(AttributeType::Bool, 'default' => 1),
            );
        }

    /**
     * Render the field settings
     * @return none
     */
    public function getSettingsHtml()
    {
        $locale = craft()->language;
        $siteMeta = craft()->seomatic->getSiteMeta($locale);

        $fields = craft()->fields->getAllFields();

        $fieldList = array('title' => 'Title');
        $imageFieldList = array();
        foreach ($fields as $field)
        {

            switch ($field->type)
            {
                case "PlainText":
                case "RichText":
                case "RedactorI":
                    $fieldList[$field->handle] = $field->name;
                    break;

                case "Matrix":
                    $fieldList[$field->handle] = $field->name;
                    break;

                case "Tags":
                    $fieldList[$field->handle] = $field->name;
                    break;

                case "Assets":
                    $imageFieldList[$field->handle] = $field->name;
                    break;
            }
        }

        $titleLength = craft()->config->get("maxTitleLength", "seomatic");
        if ($siteMeta['siteSeoTitlePlacement'] == "none")
            $titleLength = $titleLength;
        else
            $titleLength = ($titleLength - strlen(" | ") - strlen($siteMeta['siteSeoName']));

        craft()->templates->includeCssResource('seomatic/css/style.css');
        craft()->templates->includeCssResource('seomatic/css/field.css');
        craft()->templates->includeJsResource('seomatic/js/field_settings.js');

        $assetElementType = craft()->elements->getElementType(ElementType::Asset);
        return craft()->templates->render('seomatic/field_settings', array(
            'assetSources'          => $this->getElementSources($assetElementType),
            'fieldList'             => $fieldList,
            'imageFieldList'        => $imageFieldList,
            'titleLength'           => $titleLength,
            'settings'              => $this->getSettings()
        ));
   }

    /**
     * [prepValueFromPost description]
     * @param  [type] $value [description]
     * @return none          n/a
     */
    public function prepValueFromPost($value)
    {
        $result = null;

        if (empty($value))
        {
            $result = $this->prepValue($value);
        }
        else
        {
            $result = new Seomatic_MetaFieldModel($value);
        }
        return $result;
    }

    public function prepValue($value)
    {
        if (!$value)
        {
            $value = new Seomatic_MetaFieldModel();

            $value->seoTitle = $this->getSettings()->seoTitle;
            $value->seoTitleSource = $this->getSettings()->seoTitleSource;
            $value->seoTitleSourceField = $this->getSettings()->seoTitleSourceField;

            $value->seoDescription = $this->getSettings()->seoDescription;
            $value->seoDescriptionSource = $this->getSettings()->seoDescriptionSource;
            $value->seoDescriptionSourceField = $this->getSettings()->seoDescriptionSourceField;

            $value->seoKeywords = $this->getSettings()->seoKeywords;
            $value->seoKeywordsSource = $this->getSettings()->seoKeywordsSource;
            $value->seoKeywordsSourceField = $this->getSettings()->seoKeywordsSourceField;

            $value->seoImageIdSource = $this->getSettings()->seoImageIdSource;
            $value->seoImageIdSourceField = $this->getSettings()->seoImageIdSourceField;

            $value->twitterCardType = $this->getSettings()->twitterCardType;
            $value->openGraphType = $this->getSettings()->openGraphType;

            $value->robots = $this->getSettings()->robots;
        }

        if (craft()->request->isSiteRequest())
        {
        }

        return $value;
    }

    /**
     * @inheritDoc IFieldType::onAfterElementSave()
     *
     * @return null
     */
    public function onAfterElementSave()
    {
        $element = $this->element;
        $content = $element->getContent();
        $fieldHandle = $this->model->handle;
        $shouldResave = false;

        if (empty($fieldHandle))
            $shouldResave = true;
        if (!isset($content[$fieldHandle]))
            $shouldResave = true;
        else
        {
            if (empty($content[$fieldHandle]))
                $shouldResave = true;
        }

        if ($shouldResave)
        {
            $defaultField = $this->prepValue(null);
            $content->setAttribute($fieldHandle, $defaultField);
            $element->setContent($content);
            craft()->content->saveContent($element);
        }

        parent::onAfterElementSave();
    }

    /**
     * Returns sources avaible to an element type.
     *
     * @access protected
     * @return mixed
     */
    protected function getElementSources($elementType)
    {
        $sources = array();

        foreach ($elementType->getSources() as $key => $source)
        {
            if (!isset($source['heading']))
            {
                $sources[] = array('label' => $source['label'], 'value' => $key);
            }
        }

        return $sources;
    }

} /* -- Seomatic_MetaFieldType */