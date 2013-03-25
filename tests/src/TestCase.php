<?php
namespace Tests;

class TestCase extends FactoryMock
{

    private static $boolArray = array(false => 'false', true => 'true');
    private $tabTricks = array();

    /**
     * @param MockArg[] $tabMethodes
     * @return array
     */
    protected function genererEvals($tabMethodes)
    {
        $tabEvals = array();

        foreach ($tabMethodes as $uneMethode) {
            $methode = "->method(\"" . $uneMethode->getMethode() . "\")";

            $with = $this->makeWith($uneMethode->getArguments());
            $will = $this->makeWill($uneMethode->getReturnValeur());

            $tabEvals[$uneMethode->getMethode()][] = array('methode' => $methode, 'with' => $with, 'will' => $will);
        }

        return $tabEvals;
    }

    /**
     * @param \PHPUnit_Framework_MockObject_MockObject $mock
     * @param string $enteteMock
     * @param array $tabEvals
     * @return \PHPUnit_Framework_MockObject_MockObject|mixed
     */
    protected function informerMock($mock, $enteteMock, $tabEvals)
    {
        $cptAt = 0;
        foreach ($tabEvals as $methodeEval) {
            if (count($methodeEval) == 1) {
                $methode = $methodeEval[0]['methode'];
                $with = $methodeEval[0]['with'];
                $will = $methodeEval[0]['will'];

                eval("$enteteMock(\$this->atLeastOnce())$methode$with$will");

                $cptAt++;
            } else {
                foreach ($methodeEval as $evalAEffectuer) {
                    $methode = $evalAEffectuer['methode'];
                    $with = $evalAEffectuer['with'];
                    $will = $evalAEffectuer['will'];
                    eval("$enteteMock(\$this->at($cptAt))$methode$with$will");

                    $cptAt++;
                }
            }
        }

        $this->tabTricks = array();

        return $mock;
    }

    protected function createStaticMock($type)
    {
        $tabEvals = $this->genererEvals(array_slice(func_get_args(), 1));

        $mock = $this->recupererMockSelonNom($type, array_unique(array_keys($tabEvals)));

        return $this->informerMock($mock, "\$mock::staticExpects", $tabEvals);
    }

    protected function createMock($type)
    {
        $tabEvals = $this->genererEvals(array_slice(func_get_args(), 1));

        $mock = $this->recupererMockSelonNom($type, array_unique(array_keys($tabEvals)));

        return $this->informerMock($mock, "\$mock->expects", $tabEvals);
    }

    private function getPlainVar($element)
    {
        if (is_bool($element)) {
            $var = self::$boolArray[$element];
        } elseif (is_array($element)) {
            $var = $this->arrayToStringPhp($element);
        } elseif (is_object($element)) {
            $this->tabTricks[] = $element;
            end($this->tabTricks);
            $var = "\$this->tabTricks[" . key($this->tabTricks) . "]";
        } elseif (is_numeric($element)) {
            $var = $element;
        } else {
            $var = '"' . addslashes($element) . '"';
        }

        return $var;
    }

    private function makeWith($tabArguments)
    {
        if (!isNull($tabArguments)) {
            $with = "->with(";

            $with .= implode(", ", array_map(array($this, 'getPlainVar'), $tabArguments));

            $with .= ")";
        } else {
            $with = "";
        }

        return $with;
    }

    private function makeWill($element)
    {
        if (!isNull($element)) {
            if ($element instanceof \Exception) {
                $returnType = 'throwException';
            } elseif (!is_callable($element)) {
                $returnType = 'returnValue';
            } else {
                $returnType = 'returnCallback';
            }

            $will = "->will(\$this->" . $returnType . "(" . $this->getPlainVar($element) . "));";
        } else {
            $will = ";";
        }

        return $will;
    }

    private function arrayToStringPhp(array $array)
    {
        $string = 'array(';
        foreach ($array as $clef => $valeur) {
            $string .= '"' . $clef . '" => ' . $this->getPlainVar($valeur) . ',';
        }

        return substr($string, 0, -1) . ')';
    }
}