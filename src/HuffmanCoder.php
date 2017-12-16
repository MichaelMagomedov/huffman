<?php

use Nodes\HuffmanNode;
use Utils\BinaryOpertions;
use Utils\Entr;
use Nodes\Node;
use Utils\Math;

class HuffmanCoder
{
    /** @var  string $path */
    protected $path;

    /** @var array $codes */
    protected $codes;

    /**
     * HuffmanCoder constructor.
     * @param string $path
     */
    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * @param int $charCount
     * @return array
     * @throws Exception
     */
    public function encode(int $charCount):string
    {
        $nodes = $this->getNodes($charCount); // Получаем ноды
        $codes = [];  // Коды символов
        $huffNodes = [];

        //получаемн новы хавмана
        foreach ($nodes as $node) {
            array_push($huffNodes, new HuffmanNode(null, null, $node->symbol, $node->probability));
        }

        while (sizeof($huffNodes) > 1) { // Пока не нашли корень
            /** @var HuffmanNode $first */
            $first = $this->takeNode($huffNodes);  // Берем ноду с наименьшей вероятностью
            $this->removeHuffNode($first, $huffNodes);
            /** @var HuffmanNode $first */
            $second = $this->takeNode($huffNodes); // Аналогично берем еще одну
            $this->removeHuffNode($second, $huffNodes);
            // Создаем новую ноду, суммируем символы и вероятности, первая и вторая нода становятся левым и правым поддеревом
            $newNode = new HuffmanNode($first, $second, $first->symbol . $second->symbol, $first->probability + $second->probability);
            array_push($huffNodes, $newNode); // Заносим ноду обратно в список
        }


        $queue = [array_values($huffNodes)[0]];

        while (!empty($queue)) {

            $item = array_pop($queue);

            if ($item->left != null) {
                $item->left->binary = $item->binary . "0";
                array_push($queue, $item->left);
            }

            if ($item->right != null) {
                $item->right->binary = $item->binary . "1";
                array_push($queue, $item->right);
            }

            if (empty($item->right) && empty($item->left)) {
                $codes[$item->symbol] = $item->binary;
            }
        }
        $this->codes = $codes;
        return $this->codeFile($charCount, $codes);
    }

    /**
     * @param int $charCount
     * @param array $codes
     * @return bool
     */
    protected function codeFile(int $charCount, array $codes):bool
    {
        $text = file_get_contents($this->path);
        $encode = [];
        $syll = null;

        for ($start = 0; $start <= strlen($text) - $charCount; $start += $charCount) {
            $syll = substr($text, $start + $charCount - 1, $charCount);    // Считываем указаное кол-во символов (1 или 2)
            array_push($encode, $codes[$syll]); // Заносим в список код 1 или 2 символов
        }

        $bytes = BinaryOpertions::convertBitSetToByteStr($encode);
        BinaryOpertions::fwriteByteStream($this->path . ".huf", $bytes);

        return true;
    }


    public function decodeFile(string $path, array $codes = null):string
    {
        if (empty($codes) && empty($this->codes)) {
            throw new \Exception("codes table not found");
        } elseif (empty($codes) && !empty($this->codes)) {
            $codes = $this->codes;
        }

        $bits = BinaryOpertions::readBitsDataFromFile($path);
        $buff = "";
        $resultStr = "";

        for ($i = 0; $i < strlen($bits); $i++) {
            $buff .= $bits[$i];
            foreach ($codes as $char => $code) {
                if ($code === $buff) {
                    $buff = "";
                    $resultStr .= $char;
                }
            }
        }
        return $resultStr;
    }

    /**
     * @param HuffmanNode $node
     * @param array $huffmanNodes
     * @throws Exception
     */
    protected function removeHuffNode(HuffmanNode $node, array &$huffmanNodes)
    {
        if (($key = array_search($node, $huffmanNodes)) !== FALSE) {
            unset($huffmanNodes[$key]);
        } else {
            throw new \Exception("узел хафмана не найден");
        }
    }

    /**
     * @param int $charCount
     * @return array
     * @throws Exception
     */
    protected function getNodes(int $charCount):array
    {
        $nodes = [];
        $entrEngine = new Entr($this->path);

        switch ($charCount) {
            case 1:

                $entrMap = $entrEngine->getCharProbabilityMap();
                foreach ($entrMap as $char => $prob)
                    array_push($nodes, new Node($char, $prob));

                break;

            case 2:

                $entrMap = $entrEngine->getSLogProbabilityMap();
                foreach ($entrMap as $char => $prob)
                    array_push($nodes, new Node($char, $prob));


                break;

            default:
                throw new \Exception("неизвестная форма кодирования");
                break;
        }

        return $nodes;
    }

    /**
     * @param array $nodes
     * @return HuffmanNode
     */
    protected function takeNode(array $nodes):HuffmanNode
    {
        $prob = 1;
        $result = null;
        foreach ($nodes as $node) {
            if ($node->probability < $prob) {
                $prob = $node->probability;
                $result = $node;
            }
        }

        return $result;
    }

    /**
     * @param string $text
     * @param int $size
     * @return array
     */
    protected function split(string $text, int $size):array
    {
        $buf = [];
        for ($i = 0; $i < strlen($text); $i += $size) {
            array_push($buf, substr($text, $i, Math::min($size, strlen($text))));
        }

        return $buf;
    }

    /**
     * @param array $nodes
     * @return float
     */
    protected function sum(array $nodes):float
    {
        $sum = 0;
        foreach ($nodes as $node) {
            $sum += $node->probability;
        }
        return $sum;
    }


}