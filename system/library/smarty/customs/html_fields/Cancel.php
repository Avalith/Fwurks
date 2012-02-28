<?php

require_once dirname(__FILE__).'/Button.php';

class HtmlForm_CancelField extends HtmlForm_ButtonField
{
	protected function reinit(){ parent::reinit(); $this->type = 'button'; }
}

?>