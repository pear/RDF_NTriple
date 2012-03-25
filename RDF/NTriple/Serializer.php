<?php
// ----------------------------------------------------------------------------------
// Class: RDF_NTriple_Serializer
// ----------------------------------------------------------------------------------
/**
 * PHP N-Triple Serializer
 *
 * This class serialises models to N-Triple Syntax.
 *
 * @author Daniel Westphal <mail@d-westphal.de>
 * @version V0.7
 * @package syntax
 * @access public
 */

class RDF_NTriple_Serializer extends RDF_Object
{
    var $debug;
    var $model;
    var $res;

    public function __construct()
    {
        $this->debug = false;
    }

    /**
     * Serializes a model to N Triple syntax.
     *
     * @param object Model $model
     * @return string
     * @access public
     */
    function serialize($m)
    {
        if (!is_a($m, 'RDF_Model_Memory')) {
            $m = $m->getMemModel();
        }

        $this->reset();

        foreach ($m->triples as $t) {
            $s = $t->getSubject();
            if (is_a($s, 'RDF_BlankNode')) {
                $subject = '_:' . $s->getURI();
            } else {
                $subject = '<' . str_replace(' ', '', $s->getURI()) . '>';
            }

            $p = $t->getPredicate();
            $predicate = '<' . str_replace(' ', '', $p->getURI()) . '>';

            $o = $t->getObject();
            if (is_a($o, 'RDF_Literal')) {
                $object = '"' . $o->getLabel() . '"';
                if ($o->getLanguage() != '') {
                    $object .= '@' . $o->getLanguage();
                }
                if ($o->getDatatype() != '') {
                    $object .= '^^<' . $o->getDatatype() . '>';
                }
            } elseif (is_a($o, 'RDF_BlankNode')) {
                $object = '_:' . $o->getURI();
            } else {
                $object = '<' . str_replace(' ', '', $o->getURI()) . '>';
            } ;

            $this->res .= $subject . ' ' . $predicate . ' ' . $object . ' .';
            $this->res .= RDF_LINEFEED . RDF_LINEFEED;
        }

        return $this->res;
    }

    /**
     * Serializes a model and saves it into a file.
     * Returns FALSE if the model couldn't be saved to the file.
     *
     * @access public
     * @param object Model_Memory $model
     * @param string $filename
     * @return boolean
     * @access public
     */
    function saveAs(Model_Memory $model, $filename)
    {
        // serialize model
        $n3 = $this->serialize($model);
        // write serialized model to file
        $file_handle = @fopen($filename, 'w');
        if ($file_handle) {
            fwrite($file_handle, $n3);
            fclose($file_handle);
            return true;
        }
        return false;
    
    }

    /**
     * Readies this object for serializing another model
     *
     * @param void
     * @returns void
     */
    protected function reset()
    {
        $this->res = "";
        $this->model = null;
    }
}
