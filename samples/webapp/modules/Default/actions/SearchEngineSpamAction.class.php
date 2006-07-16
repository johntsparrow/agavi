<?php

class Default_SearchEngineSpamAction extends AgaviAction
{
	public function execute(AgaviParameterHolder $parameters)
	{
		$this->getContext()->getRequest()->setAttribute('product_name', $parameters->getParameter('name'));
		return 'Success';
	}
}

?>