<?php

/**
 * @link http://www.vishwayon.com/
 * @copyright Copyright (c) 2020 Vishwayon Software Pvt Ltd
 * @license MIT
 */

namespace PhpStep\base;

/**
 * Contains rendering information of a pattern
 * @author girish
 */
class PatternType {
    const PATTERN_TYPE_NONE = '';
    const PATTERN_TYPE_COPY = 'copy';
    const PATTERN_TYPE_FIELD = 'field';
    const PATTERN_TYPE_EACH = 'eachLoop';
    
    private string $pType = '';
    
    public string $propName = '';
    public array $propNameList = [];
    public array $tranInfo = [];
    
    /**
     * Contains the CurrentCell Value only for Type_COPY 
     */
    public $currentValue;
    
    public function __construct(string $ptype) {
        $this->pType = $ptype;
    }
    
    public function getType(): string {
        return $this->pType;
    }
    
    public function setType(string $ptype) {
        $this->pType = $ptype;
    }
    
}
