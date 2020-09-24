<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\Form\Entity\Traits\Accept;
use Leon\BswBundle\Module\Form\Entity\Traits\Args;
use Leon\BswBundle\Module\Form\Entity\Traits\ButtonLabel;
use Leon\BswBundle\Module\Form\Entity\Traits\FileItems;
use Leon\BswBundle\Module\Form\Entity\Traits\Flag;
use Leon\BswBundle\Module\Form\Entity\Traits\ListType;
use Leon\BswBundle\Module\Form\Entity\Traits\NeedDrag;
use Leon\BswBundle\Module\Form\Entity\Traits\NeedId;
use Leon\BswBundle\Module\Form\Entity\Traits\NeedTips;
use Leon\BswBundle\Module\Form\Entity\Traits\Route;
use Leon\BswBundle\Module\Form\Entity\Traits\ShowList;
use Leon\BswBundle\Module\Form\Entity\Traits\Url;
use Leon\BswBundle\Module\Form\Entity\Traits\VarNameForTips;

class Upload extends Number
{
    use Route;
    use Args;
    use ButtonLabel;
    use NeedDrag;
    use Accept;
    use ShowList;
    use ListType;
    use Flag;
    use FileItems;
    use Url;
    use NeedId;
    use NeedTips;
    use VarNameForTips;

    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->setChange('uploaderChange');
        $this->setButtonLabel('Click to select for upload');
        $this->setVarNameForTips('persistenceUploadTipsCollect');
    }
}