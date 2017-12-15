<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\ProductLabelPageSearch\Communication\Plugin\PageDataExpander;

use Generated\Shared\Transfer\ProductPageSearchTransfer;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;
use Spryker\Zed\ProductPageSearch\Dependency\Plugin\ProductPageDataExpanderInterface;

/**
 * @method \Spryker\Zed\ProductLabelPageSearch\Communication\ProductLabelPageSearchCommunicationFactory getFactory()
 */
class ProductLabelPageDataExpanderPlugin extends AbstractPlugin implements ProductPageDataExpanderInterface
{

    /**
     * @param array $productData
     * @param \Generated\Shared\Transfer\ProductPageSearchTransfer $productAbstractPageSearchTransfer
     *
     * @return void
     */
    public function expandProductPageData(array $productData, ProductPageSearchTransfer $productAbstractPageSearchTransfer)
    {
        $labelIds = $this->getFactory()->getProductLabelFacade()->findLabelIdsByIdProductAbstract($productData['fk_product_abstract']);
        $productAbstractPageSearchTransfer->setLabelIds($labelIds);
    }

}
