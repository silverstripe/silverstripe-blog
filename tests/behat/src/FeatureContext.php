<?php

namespace SilverStripe\Blog\Tests\Behat\Context;

use SilverStripe\BehatExtension\Context\SilverStripeContext;
use Behat\Mink\Element\NodeElement;
use PHPUnit\Framework\Assert;

class FeatureContext extends SilverStripeContext
{
    /**
     * Adds a widget to the blog
     *
     * @Then /^I add the "([^"]+)" widget$/
     * @param $widgetTitle e.g. "Content"
     */
    public function iAddTheWidget($widgetTitle)
    {
        $page = $this->getSession()->getPage();
        $h3s = $page->findAll('css', '.availableWidgetsHolder h3');
        $found = false;
        foreach ($h3s as $h3) {
            if ($h3->getText() !== $widgetTitle) {
                continue;
            }
            $found = true;
            $h3->click();
        }
        Assert::assertTrue($found, "Widget {$widgetTitle} was not found");
    }

    /**
     * Fills in a field within a widget
     *
     * @Then /^I fill in the "([^"]+)" widget field "([^"]+)" with "([^"]+)"$/
     * @param $widgetTitle e.g. "Content"
     * @param $htmlFragment e.g. "Title"
     * @param $value e.g. "Lorem ipsum"
     */
    public function iFillInTheWidgetField($widgetTitle, $fieldTitle, $value)
    {
        $page = $this->getSession()->getPage();
        $widget = $this->getWidget($widgetTitle);
        $field = $widget->findField($fieldTitle);
        Assert::assertNotNull($field, "Widget field {$fieldTitle} was not found");
        $field->setValue($value);
    }

    /**
     * Adapated from framework CmsFormsContext stepIFillInTheHtmlFieldWith
     *
     * @When /^I fill in the "([^"]+)" widget HTML field "([^"]+)" with "([^"]+)"$/
     */
    public function stepIFillInTheHtmlFieldWith($widgetTitle, $fieldTitle, $value)
    {
        $widget = $this->getWidget($widgetTitle);
        $field = $this->getDescendantHtmlField($widget, $fieldTitle);
        $this->getSession()->evaluateScript(sprintf(
            "jQuery('#%s').entwine('ss').getEditor().setContent('%s')",
            $field->getAttribute('id'),
            addcslashes($value, "'")
        ));
        $this->getSession()->evaluateScript(sprintf(
            "jQuery('#%s').entwine('ss').getEditor().save()",
            $field->getAttribute('id')
        ));
    }

    /**
     * @return NodeElement|null
     */
    private function getWidget($widgetTitle)
    {
        $ret = null;
        $widgets = $this->getSession()->getPage()->findAll('css', '.usedWidgets .Widget');
        foreach ($widgets as $widget) {
            $h3 = $widget->find('css', 'h3');
            if (!$h3 || $h3->getText() !== $widgetTitle) {
                continue;
            }
            $ret = $widget;
            break;
        }
        Assert::assertNotNull($ret, "Widget edit form for {$widgetTitle} was not found");
        return $ret;
    }

    /**
     * @param NodeElement $ancestor
     * @param string $locator
     * @return NodeElement|null
     */
    private function getDescendantHtmlField($element, $locator)
    {
        $textarea = $element->find('css', "textarea.htmleditor[name='{$locator}']");
        if (is_null($textarea)) {
            $labels = $element->findAll('xpath', "//label[contains(text(), '{$locator}')]");
            Assert::assertCount(1, $labels, "Found more than one html field label containing the phrase '{$locator}}'");
            $label = array_shift($labels);
            $textarea = $element->find('css', '#' . $label->getAttribute('for'));
        }
        Assert::assertNotNull($textarea, "HTML field {$locator} not found");
        return $textarea;
    }
}
