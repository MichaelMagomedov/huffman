<?php

namespace Nodes;

class HuffmanNode extends Node
{

    /** @var HuffmanNode $left */
    public $left;
    /** @var HuffmanNode $right */
    public $right;
    /** @var  string $binary */
    public $binary;

    /**
     * HaffmanNode constructor.
     * @param HuffmanNode $left
     * @param HuffmanNode $right
     */
    public function __construct(HuffmanNode $left = null, HuffmanNode $right = null, string $symb, float $prob, string $binary = "")
    {
        parent::__construct($symb, $prob);
        $this->left = $left;
        $this->right = $right;
        $this->binary = $binary;
    }


}