<?php
namespace AppBundle\Model\Product\CalculatedValue;

use AppBundle\Model\Product\Car;
use Pimcore\Model\DataObject\Concrete;
use Pimcore\Model\DataObject\Data\CalculatedValue;
use Pimcore\Tool;

class QualityCalculator {


    /**
     * @param $object Concrete
     * @param $context CalculatedValue
     * @return string
     */
    public static function compute(Concrete $object, CalculatedValue $context) {

          return self::getCalculatedValueForEditMode($object, $context);

    }

    /**
     * @param $object
     * @param $context CalculatedValue
     * @return string
     */
    public static function getCalculatedValueForEditMode(Concrete $object, CalculatedValue $context) {

        if($object instanceof Car) {
            if($context->getFieldname() == "textsAvailable") {

                if($object->getName($context->getPosition()) && $object->getDescription($context->getPosition())) {
                    return "completed";
                }

            }

            if($context->getFieldname() == "attributesAvailable") {

                if($object->getAttributes() && $object->getAttributes()->getItems()) {
                    return "completed";
                }

            }

            if($context->getFieldname() == "saleInformationAvailable") {

                if($object->getSaleInformation() && $object->getSaleInformation()->getSaleInformation()) {
                    return "completed";
                }

            }

            if($context->getFieldname() == "imagesAvailable") {

                if($object->getMainImage()) {
                    return "completed";
                }

            }

            return "not completed";

        } else {
            return null;
        }

    }

    /**
     * @param $data
     * @param $object
     * @param $params
     * @return string
     */
    public static function renderLayoutText($data, Concrete $object, $params) {

        if($object instanceof Car) {
            $quality = [];

            $hasMissing = false;
            $hasCompleted = false;
            foreach(Tool::getValidLanguages() as $language) {
                if($object->getTextsAvailable($language) == "not completed") {
                    $hasMissing = true;
                } else if($object->getTextsAvailable($language) == "completed") {
                    $hasCompleted = true;
                }
            }

            $quality['Texts Available'] = !$hasCompleted ? "not completed" : (!$hasMissing ? "completed" : "partly completed");
            $quality['Attributes Available'] = $object->getAttributesAvailable();
            $quality['Sale Information Available'] = $object->getSaleInformationAvailable();
            $quality['Images Available'] = $object->getImagesAvailable();

            $htmlTable = "<table class='qa-summary-table'>";

            $htmlTable .= "<thead><tr><td>Elements</td><td>State</td></tr></thead>";

            foreach($quality as $key => $value) {

                $cssClass = "";
                switch ($value) {
                    case "completed":
                        $cssClass = "qa-completed";
                        break;
                    case "not completed":
                        $cssClass = "qa-not-completed";
                        break;
                    case "partly completed":
                        $cssClass = "qa-partly-completed";
                        break;

                }

                $htmlTable .= "<tr class='$cssClass'>";
                $htmlTable .= "<td>$key</td>";
                $htmlTable .= "<td>$value</td>";
                $htmlTable .= "</tr>";
            }

            $htmlTable .= "</table>";

            return "<h2 style='margin-top: 0'>Data Quality Summary</h2>" . $htmlTable;
        } else {
            return "";
        }


    }
}