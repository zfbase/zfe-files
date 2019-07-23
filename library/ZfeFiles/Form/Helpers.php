<?php

trait ZfeFiles_Form_Helpers
{
    use ZFE_Form_Helpers {
        ZFE_Form_Helpers::_initializePrefixes as ZFE_initializePrefixes;
    }

    protected function _initializePrefixes()
    {
        $this->ZFE_initializePrefixes();

        $this->addPrefixPath(
            'ZfeFiles_Form_Element',
            __DIR__ . '/Element',
            'element'
        );
    }
}
