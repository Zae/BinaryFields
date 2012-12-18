<?php
/**
 * Automatically serialize and deserialize all binary fields.
 *
 * Mark the MySQL columns as any BLOB type.
 *
 * @author Ezra <ezra@tsdme.nl>
 * @version 0.1
 */
class BinaryBehavior extends ModelBehavior {
	const FIELD_TYPE = 'type';
	const TYPE_BINARY = 'binary';
	
	/**
	 * Array of all the binary fields in the table.
	 * @var array
	 * @since 0.1
	 */
	protected $binaryFields = array();

	/**
	 * setup callback that initializes the binaryFields array.
	 *
	 * @param Model $model The Modal that gets initialized
	 * @param array $config
	 *
	 * @author Ezra Pool <ezra@tsdme.nl>
	 * @version 0.1
	 * @since 0.1
	 */
	public function setup(Model $model, $config = array()) {
		foreach($model->schema() as $key => $value){
			if($value[self::FIELD_TYPE] == self::TYPE_BINARY){
				$this->binaryFields[$model->alias][] = $key;
			}
		}
		
		return parent::setup($model, $config);
	}

	/**
	 * beforeSave callback
	 *
	 * @param Model $model
	 * @return array
	 *
	 * @author Ezra Pool <ezra@tsdme.nl>
	 * @version 0.1
	 * @since 0.1
	 */
	public function beforeSave(Model $model) {
		if(count($model->data) <= 1){
			foreach($this->binaryFields[$model->alias] as $field){
				if(!empty($model->data[$model->alias][$field])){
					$model->data[$model->alias][$field] = serialize($model->data[$model->alias][$field]);
				}
			}
		} else {
			foreach($model->data as &$result){
				foreach($this->binaryFields[$model->alias] as $field){
					if(!empty($result[$model->alias][$field])){
						$result[$model->alias][$field] = serialize($result[$model->alias][$field]);
					}
				}
			}
		}

		return parent::beforeSave($model);
	}

	/**
	 * afterFind callback
	 *
	 * @param Model $model
	 * @param array $results The results from the find query.
	 * @param int $primary The tables primary field.
	 * @return array
	 *
	 * @author Ezra Pool <ezra@tsdme.nl>
	 * @version 0.1
	 * @since 0.1
	 */
	public function afterFind(Model $model, $results, $primary = false) {
		foreach($results as &$result){
			foreach($this->binaryFields[$model->alias] as $field){
				if(!empty($result[$model->alias][$field])){
					$result[$model->alias][$field] = unserialize($result[$model->alias][$field]);
				}
			}
		}
		
		parent::afterFind($model, $results, $primary);
		return $results;
	}
}

?>
