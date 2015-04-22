<?php

namespace SprykerFeature\Zed\Ui\Dependency\Form;

use Generated\Zed\Ide\AutoCompletion;
use SprykerEngine\Shared\Kernel\LocatorLocatorInterface;
use SprykerEngine\Zed\Kernel\Persistence\AbstractQueryContainer;
use SprykerEngine\Zed\Kernel\Persistence\QueryContainer\QueryContainerInterface;
use SprykerFeature\Zed\Ui\Communication\Plugin\Form\Field;
use SprykerFeature\Zed\Ui\Communication\Plugin\Form\StateContainer\StateContainerInterface;
use SprykerFeature\Zed\Ui\Communication\Plugin\Form\SubForm;
use SprykerFeature\Zed\Ui\Dependency\Plugin\FormPluginInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator;
use Exception;

abstract class AbstractForm
{

    const STATE_NEW = 'new';
    const STATE_SUCCESS = 'success';
    const STATE_FAIL = 'fail';

    const OUTPUT_STATE = 'state';
    const OUTPUT_FIELDS = 'fields';
    const OUTPUT_PAYLOAD = 'content';

    /**
     * @var AutoCompletion
     */
    protected $locator;

    /**
     * @var AbstractQueryContainer
     */
    protected $queryContainer;

    /**
     * @var FormPluginInterface[]
     */
    protected $plugins;

    /**
     * @var StateContainerInterface
     */
    protected $stateContainer;

    /** @var bool */
    protected $isInitialized;

    /**
     * @return array
     */
    abstract protected function getDefaultData();

    abstract public function addFormFields();

    /**
     * @param LocatorLocatorInterface $locator
     * @param Request $request
     * @param QueryContainerInterface $queryContainer
     */
    public function __construct(
        Request $request,
        LocatorLocatorInterface $locator,
        QueryContainerInterface $queryContainer = null
    ) {
        $this->locator = $locator;
        $this->stateContainer = $this->locator->ui()->pluginFormStateContainerStateContainer()->setRequest($request);
        $this->queryContainer = $queryContainer;
    }

    public function init()
    {
        $preparedValues = array_merge($this->getDefaultData(), $this->stateContainer->getRequestData());

        $this->stateContainer->setActiveValues($preparedValues);

        $this->addFormFields();

        $this->isInitialized = true;
    }

    /**
     * @return bool
     */
    protected function isEveryPluginValid()
    {
        foreach ($this->plugins as $plugin) {
            if (!$plugin->isValid()) {
                return false;
            }
        }

        return true;
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isValid()
    {
        if (!$this->isInitialized) {
            throw new Exception("Form is not initialized.");
        }

        if (!$this->stateContainer->receivedSubmitRequest()) {
            return false;
        }

        return $this->isEveryPluginValid();
    }

    /**
     * @return string
     */
    protected function getState()
    {
        $state = self::STATE_NEW;
        if ($this->stateContainer->receivedSubmitRequest()) {
            $state = $this->isEveryPluginValid() ? self::STATE_SUCCESS : self::STATE_FAIL;
        }

        return $state;
    }

    /**
     * @return array|mixed
     * @throws Exception
     */
    public function renderData() {
        return [self::OUTPUT_PAYLOAD => $this->toArray()];
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (!$this->isInitialized) {
            throw new Exception("Form is not initialized.");
        }

        $output = [];
        foreach ($this->plugins as $plugin) {
            $output = $plugin->extendOutput($output);
        }

        $output[self::OUTPUT_STATE] = $this->getState();

        return $output;
    }

    /**
     * @param $name
     * @return Field
     */
    public function addField($name)
    {
        $field = $this->locator->ui()->pluginFormField();

        $field->setName($name);

        $field->setStateContainer($this->stateContainer);

        $this->plugins[$name] = $field;

        return $field;
    }

    /**
     * @param $name
     * @return SubForm
     */
    public function addSubForm($name)
    {
        $subForm = $this->locator->ui()->pluginFormSubForm();

        $subForm->setName($name);

        $subForm->setStateContainer($this->stateContainer);

        $this->plugins[$name] = $subForm;

        return $subForm;
    }

    /**
     * @param $name
     * @return SubForm
     */
    public function getSubFormByName($name)
    {
        return $this->plugins[$name];
    }

    /**
     * @param $name
     * @return Field
     */
    public function getFieldByName($name)
    {
        return $this->plugins[$name];
    }

    /**
     * @return array|mixed
     */
    public function getRequestData()
    {
        return $this->stateContainer->getRequestData();
    }

    /**
     * @return array
     */
    public function setActiveValuesToDefault()
    {
        $defaultData = $this->getDefaultData();

        return $this->stateContainer->setActiveValues($defaultData);
    }
}
