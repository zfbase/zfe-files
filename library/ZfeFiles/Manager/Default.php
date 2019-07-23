<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

/**
 * Стандартный менеджер с единственным полем для загрузки любых файлов.
 */
class ZfeFiles_Manager_Default extends ZfeFiles_Manager_Abstract
{
    /**
     * {@inheritdoc}
     */
    public function getFieldsSchemas(): ZfeFiles_Schema_Collection
    {
        $schemas = new ZfeFiles_Schema_Collection;
        $schemas->add(new ZfeFiles_Schema);
        return $schemas;
    }

    /**
     * {@inheritdoc}
     */
    protected function initAccessor(Zend_Acl $acl, ZFE_Model_Default_Editors $user): ZfeFiles_Accessor
    {
        return new ZfeFiles_Accessor_Acl($acl, $user);
    }
}
