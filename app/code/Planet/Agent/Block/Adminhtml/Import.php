<?php

namespace Planet\Agent\Block\Adminhtml;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Container;

class Import extends Container
{

    public function __construct(
        Context $context,
        array $data = []
    ){
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        $this->_objectId = 'entity_id';
        $this->_blockGroup = 'Planet_Agent';
        $this->_controller = 'adminhtml_import';

        parent::_construct();
    }

    protected function _prepareLayout()
    {
        $this->setChild(
            'grid',
            $this->getLayout()->createBlock(
                'Planet\Agent\Block\Adminhtml\Import\Grid',
                'agent.import.grid'
            )
        );

        return parent::_prepareLayout();
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    public function getCustomerHtml()
    {
        return $this->getChildHtml('customer');
    }

    /**
     * Prepare a dynamic button html to be included in the header of a customer starts.
     * This process occurs after the import of XLSX spreadsheet
     *
     * @return null|string
     */
    public function getNewOrderHtml()
    {
        if($this->getChildBlock('customer')){
            return <<< "CUSTOMER_HTML_BUTTOM"
                    <button id="save" title="Create New Order" type="button"
                            class="action- scalable save primary ui-button 
                            ui-widget ui-state-default ui-corner-all 
                            ui-button-text-only order-button"
                            aria-disabled="false"
                            >
                        <span class="ui-button-text">
                            <span>Create New Order</span></span>
                    </button>
CUSTOMER_HTML_BUTTOM;

        }

        return null;
    }

    /**
     * Prepare a dynamic form html to be included in the header of a customer starts.
     * This process occurs after the import of XLSX spreadsheet
     *
     * @return null|string
     */
    public function getOrderForm()
    {
        if($this->getChildBlock('customer')){

            // gets block
            $customerBlock = $this->getChildBlock('customer');

            // gets properties defined in block
            $mail      = $customerBlock->getEmail();
            $street    = $customerBlock->getStreet();
            $city      = $customerBlock->getCity();
            $countryId = $customerBlock->getCountyId();
            $postcode  = $customerBlock->getPostcode();
            $telephone = $customerBlock->getTelephone();
            $region    = $customerBlock->getRegion();
            $firstname = $customerBlock->getFirstName();
            $lastname  = $customerBlock->getlastName();

            // product collection received by setData() in current block.
            $collection = $this->getCollection();

            $itemsHtml = null;

            foreach ($collection as $item) {
                if($item->hasData('id')){
                    $itemsHtml .= <<< "ITEM"
                        <input type="hidden" name="items[]"   
                        value="{$item->getData('id')}-{$item->getData('quantity')}"/>\n
ITEM;
                }
            }

            // return a new form with imported data by xlsx file.
            return <<< "ORDER_HTML_FORM"
            <form action="{$this->getUrl("agent/*/neworder")}"
              class="admin__fieldset form-submit hidden"
              method="post"
            >
                <input type="hidden" name="form_key"   value="{$this->getFormKey()}"/>
                <input type="hidden" name="email"      value="{$mail}"/>
                <input type="hidden" name="street"     value="{$street}"/>
                <input type="hidden" name="city"       value="{$city}"/>
                <input type="hidden" name="country_id" value="{$countryId}"/>
                <input type="hidden" name="postcode"   value="{$postcode}"/>
                <input type="hidden" name="telephone"  value="{$telephone}"/>
                <input type="hidden" name="region"     value="{$region}"/>
                <input type="hidden" name="firstname"  value="{$firstname}"/>
                <input type="hidden" name="lastname"   value="{$lastname}"/>
                {$itemsHtml}
                <div class="admin__field hidden">
                    <button type="submit" class="action-secondary submit-order"
                            data-index="create_configurable_products_button">
                        <span>create order</span>
                    </button>
                </div>
            </form>
ORDER_HTML_FORM;

        }

        return null;
    }
}
