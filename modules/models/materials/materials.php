<?php
class mMaterials extends model
{
        
        // Get files from materials
	public function getFiles()
	{
		$table = Backstage::gi()->db_table_prefix.'materials';
		$this->data['items'] = $this->dbmanager->tables($table)
												->fields('*')
												->where('object_id = '.$this->data['request']->parameters['object_id'].' AND object_type = "'.$this->data['request']->parameters['object_type'].'"')
												->select();
		return $this->data;
	}        

	public function deleteFiles()
	{
		$this->data['status'] = $this->dbmanager->tables(Backstage::gi()->db_table_prefix.'materials')
												->where('object_id = '.$this->data['request']->parameters['object_id'].' and object_type = "'.$this->data['request']->parameters['object_type'].'"')
												->delete();		
		return $this->data;	
	}
	
	public function deleteFile()
	{
		$table = Backstage::gi()->db_table_prefix.'materials';
		$this->data['item'] = $this->dbmanager->tables($table)
												->fields('*')
												->where('id = '.$this->data['request']->parameters['id'])
												->getScalar();
		
		$this->data['status'] = $this->dbmanager->tables($table)
												->where('id = '.$this->data['request']->parameters['id'])
												->delete();
		return $this->data;
	}            
        
	public function saveFiles()
	{
		$table = Backstage::gi()->db_table_prefix.'materials';

		$files_first_id = $this->dbmanager->getScalarByQuery("SHOW TABLE STATUS LIKE '$table'");
		$files_first_id = (int)$files_first_id->Auto_increment;
		$this->data['files_first_id'] = $files_first_id;
		$file_id = $files_first_id;
		
		if (!isset($this->data['request']->parameters['files']))
			$this->data['request']->parameters['files'] = array();
		
		$this->data['save_result'] = '';                    
		foreach($this->data['request']->parameters['files'] as $key=>$file)
		{
			$file_path = $file['name'];
			$file_ext = substr($file_path, strrpos($file_path, '.'));
			if ($file['id'] != 0)
				$file_id = $file['id'];
			else
				$file_id = $files_first_id++;
			$file['ordering'] = 0;
			$file['object_id'] = $this->data['request']->parameters['object_id'];
			$file['object_type'] = $this->data['request']->parameters['object_type'];
			if (!isset($file['material_title']))
				$file['material_title'] = '';
			if (!isset($file['material_insert_date']))
				$file['material_insert_date'] = date('Y-m-d H:i:s');
			$file['material_type'] = 'image';
			$file['material_path'] = $file_id.$file_ext;
			
			if ($file['id'] != 0)
			{
				$this->data['save_result'] .= $this->dbmanager->tables($table)
															->values($file)
															->update();
			}
			else
			{
				$this->data['save_result'] .= $this->dbmanager->tables($table)
															->values($file)
															->insert();
			}
		}
		return $this->data;
	}
}