<?php

class Add_MVC_Controller extends Mvc_Controller{
	
	/**
	 * Function will 
	 */
	public function index($data){
//		echo $form_name;
		$type = array_shift($data);
		$form_data = array_shift($data);
		$form_files = @array_shift($data);
		
		
		$submission_type = Mvc_Db::findOrDispense('submission_type',"name = :submission_type", array(
			'submission_type' => $type
		));
//		var_dump($type);exit;
		$submission_type = array_shift($submission_type);
//		var_dump($submission_type->isEmpty());exit;
		if($submission_type->isEmpty()){
			$submission_type->name = $type;
			$submission_type->table_name = strtolower(str_replace(' ', '', $type));
			Mvc_Db::store($submission_type);
		}
//		echo $submission_type->table_name;exit;
		$submission = Mvc_Db::dispense($submission_type->table_name);
		foreach($form_data as $key => $value){
			$submission->$key = $value;
		}
		$submission->ownSubmission_type = $submission_type;

		Mvc_Db::store($submission);
		
		// Code for creating email
		/* @var $mailer Package_Mail */
		$mailer = Package_Mail::getInstance();
		
		$mail = new G2_TwigView('emails/admin');
		$mail->set('data',$form_data);
		$mail->set('submission_type',$type);
		
		$message = $mail->get_render();
				
		$mailer->add(['administrators'],"$type submission received",$message,$form_files);
		
		return true;
	}
	
}