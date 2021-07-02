<?php

namespace Leon\BswBundle\Module\Form\Entity;

use Leon\BswBundle\Module\GetSetter;

class Upload extends Number
{
    use GetSetter\Route;
    use GetSetter\Args;
    use GetSetter\ButtonLabel;
    use GetSetter\NeedDrag;
    use GetSetter\Accept;
    use GetSetter\ShowList;
    use GetSetter\ListType;
    use GetSetter\Flag;
    use GetSetter\FileItems;
    use GetSetter\Url;
    use GetSetter\NeedId;
    use GetSetter\NeedTips;
    use GetSetter\VarNameForTips;

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