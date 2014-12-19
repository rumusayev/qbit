<?php
class cMaterials extends controller
{	 
		// Get files from materials
	public function getFiles()
	{
		$this->data = Loader::gi()->getModel($this->data);	
		$files_sequence = '';
		$this->data['first_file'] = '';
		$fields = array('id', 'catalog_id', 'material_title', 'large_image', 'thumb_image');
		 
		foreach ($this->data['items'] as $subkey=>$file)
		{
			$object_type = isset($file->object_type)?$file->object_type:'';
			$object_id = isset($file->object_id)?$file->object_id:'';
			$material_insert_date = isset($file->material_insert_date)?$file->material_insert_date:date('Y-m-d G:i:s');
			$large_image = isset($file->material_path)?Backstage::gi()->MATERIALS_URL.$object_type.'/'.$object_id.'/large/'.$file->material_path:'';
			$thumb_image = isset($file->material_path)?Backstage::gi()->MATERIALS_URL.$object_type.'/'.$object_id.'/thumbnail/'.$file->material_path:'';

			$file->large_image = $large_image;
			$file->thumb_image = $thumb_image;
			if (isset($this->data['request']->parameters['design']))
			{
				$design = $this->data['request']->parameters['design']; 
				$item = $design->structure;
				foreach ($this->data['request']->parameters['design']->structure_rules as $rule_key => $rule)
				{
					if (in_array($rule[2], $fields))
						$item = str_replace($rule[1], $file->$rule[2], $item);
				}
			}
			else
			{
				$file_data = $this->data;
				$file_data['file'] = $file;
				$file_data['view_name'] = 'file';
				$item = Loader::gi()->getView($file_data);
			}
			$files_sequence .= $item;
			if ($subkey === 0)
				$this->data['first_file'] = $files_sequence;
			
		}
		if (isset($this->data['request']->parameters['design']))
		{
			$block = $this->data['request']->parameters['design']->block;
			foreach ($this->data['request']->parameters['design']->block_rules as $rule_key => $rule)
			{
				$block = str_replace('[[structure]]', $files_sequence, $block);
			}
			$this->data['files'] = $block;
		}
		else			
			$this->data['files'] = $files_sequence;
		$this->data['body'] = $this->data['items'];
		return $this->data;
	}
		
	public function saveFiles()
	{
		// Model loading                
		$this->data = Loader::gi()->getModel($this->data);	

		$object_type = $this->data['request']->parameters['object_type'];
		$object_id = $this->data['request']->parameters['object_id'];
		
		if (empty($this->data['request']->parameters['files']))
		{
			$this->data['body'] = 'no files';
			return $this->data;		
		}
		if (!is_dir(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id))
		{
			mkdir(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id, 0755);
			mkdir(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id.'/large', 0755);
			mkdir(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id.'/thumbnail', 0755);
		}

		$files_first_id = $this->data['files_first_id'];
		foreach($this->data['request']->parameters['files'] as $id=>$file)
		{
			$file_name = $file['name'];
			$file_ext = substr($file_name, strrpos($file_name, '.'));
			$file_path = Backstage::gi()->MATERIALS_DIR.'temp/files/'.$this->data['request']->parameters['session_id'].'/'.$file_name;
			$file_url = Backstage::gi()->MATERIALS_URL.'temp/files/'.$this->data['request']->parameters['session_id'].'/'.$file_name;
			if (!file_exists($file_path)) continue;
			if ($file['id'] != 0)
				$file_id = $file['id'];
			else
				$file_id = $files_first_id++;                    
			$file['material_path'] = $file_id.$file_ext;

			$image_obj = new SimpleImage($file_url);
			$image_obj->maxarea(Backstage::gi()->image_max_width, Backstage::gi()->image_max_height);
			$image_obj->save(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id.'/large/'.$file['material_path']);

			$image_obj->maxarea(Backstage::gi()->image_thumb_max_width, Backstage::gi()->image_thumb_max_height);
			$image_obj->save(Backstage::gi()->MATERIALS_DIR.$object_type.'/'.$object_id.'/thumbnail/'.$file['material_path']);
		}
		$this->deleteDirectory(Backstage::gi()->MATERIALS_DIR.'temp/files/'.session_id());
		$this->data['body'] = $this->data['save_result'];
		return $this->data;
	}        

	public function deleteFiles()
	{	
		$this->data = Loader::gi()->getModel($this->data);
		$this->deleteDirectory(Backstage::gi()->MATERIALS_DIR.$this->data['request']->parameters['object_type'].'/'.$this->data['request']->parameters['object_id'].'/');
		return $this->data;
	}	
		
	public function deleteFile()
	{
		$this->data = Loader::gi()->getModel($this->data);

		$this->deleteDirectory(Backstage::gi()->MATERIALS_DIR.$this->data['item']->object_type.'/'.$this->data['item']->object_id.'/large/'.$this->data['item']->material_path);
		$this->deleteDirectory(Backstage::gi()->MATERIALS_DIR.$this->data['item']->object_type.'/'.$this->data['item']->object_id.'/thumbnail/'.$this->data['item']->material_path);
		$this->data['body'] = $this->data['status'];
		return $this->data;
	}           
                
	private function deleteDirectory($dir)
	{
		if (!file_exists($dir)) return true;
		if (!is_dir($dir)) return unlink($dir);
		foreach (scandir($dir) as $item) 
		{
			if ($item == '.' || $item == '..') continue;
			if (!$this->deleteDirectory($dir.'/'.$item)) return false;
		}
		return rmdir($dir);
	}        
}