<?php

/*
 * ZFE – платформа для построения редакторских интерфейсов.
 */

class ZfeFiles_Schema_Texts extends ZfeFiles_Schema
{
    protected $accept = [
        'doc',
        'docx',
        'html',
        'odt',
        'pdf',
        'rtf',
        'txt',
    ];
}
