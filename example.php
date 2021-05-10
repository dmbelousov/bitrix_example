<?
function SetQntPerPack()
{

	\Bitrix\Main\Loader::includeModule('iblock');

	$elements = \CIBlockElement::GetList(
		array(),
		array(
			"IBLOCK_ID" => 21,
			"=PROPERTY_QNT_PER_PACK" => false 
		),
		false,
		false,
		array("ID","PROPERTY_QNT_PER_PACK", "IBLOCK_ID")
	);

	while($element = $elements->GetNext())
	{

		$CML2_ATTRIBUTES = CIBlockElement::GetProperty(
			$element["IBLOCK_ID"],
			$element["ID"],
			array(),
			array("CODE" => "CML2_ATTRIBUTES")
		);

		while ($arProps = $CML2_ATTRIBUTES->fetch())
		{
			switch ($arProps["DESCRIPTION"])
			{
				case "Коэффициент":
					$arQntPerPack[$element["ID"]] = (integer) $arProps["VALUE"];
					continue;
					break;
			}	
		}

	}

	$qntPerPackList = CIBlockPropertyEnum::GetList(
		array(),
		array(
			"IBLOCK_ID" => 21,
			"CODE" => "QNT_PER_PACK"
		)
	);

	while ($item = $qntPerPackList->Fetch()){
    	$qppList[$item["VALUE"]] = $item["ID"];
	}

	$propQpp = CIBlockProperty::GetByID("QNT_PER_PACK", 21)->GetNext();
	$QPP_PROP_ID = $propQpp['ID'];

	foreach($arQntPerPack as $offerId => $qpp)
	{	
		if(!isset($qppList[$qpp]))
		{
			$qppNewId = CIBlockPropertyEnum::Add(
 				array(
					"PROPERTY_ID" => $QPP_PROP_ID,
					"VALUE" => $qpp,
					"SORT" => $qpp
				)
			);
			if($qppNewId)
			{
				$qppList[$qpp] = $qppNewId;
			} 
		} 


		CIBlockElement::SetPropertyValuesEx(
            $offerId,
            "",
            array(
                "QNT_PER_PACK" => $qppList[$qpp],
            )
        );
	}

}
?>