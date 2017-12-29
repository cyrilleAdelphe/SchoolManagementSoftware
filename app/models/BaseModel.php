<?php 

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

/*
| Base Model that implements the basic functionality of models uses by individual modules
*/
class BaseModel extends Eloquent
{
	use UserTrait, RemindableTrait;

	protected $defaultOrder = array('orderBy' => 'id', 'orderOrder' => 'DESC');
	public static function getTableName()
	{
		return with (new static)->getTable();
	}

	public function getListViewData($queryString)
	{
		$model = $this->model_name;
		$result = DB::table($model::getTableName());
		$result = $result->select(array($model::getTableName().'.*'));
		//$result = $result->where($model::getTableName().'.is_active', $queryString['status']);

		if(isset($queryString['status']))
		{
			$result = $result->where($model::getTableName().'.is_active', $queryString['status']);
		}
		
		if(isset($queryString['filter']['field']) && isset($queryString['filter']['value']))
		{
			$query_columns = explode(',', $queryString['filter']['field']);
			$query_vals = explode(',', $queryString['filter']['value']);

			foreach($query_columns as $index => $col)
			{
					$result = $result->where($model::getTableName().'.'.$col, 'LIKE', '%'.$query_vals[$index].'%');	
			}
			
		}
		//}

		if(isset($queryString['orderBy']))
		{
			$result = $result->orderBy($model::getTableName().'.'.$queryString['orderBy'], $queryString['orderOrder']);
		}
		else
		{
			$result = $result->orderBy($model::getTableName().'.'.$this->defaultOrder['orderBy'], $this->defaultOrder['orderOrder']);
		}

		$result = $result->paginate($queryString['paginate']);

		$count = count($result);
		$msg = $count == 0 ? ConfigurationController::translate('No Records To Show') : '';

		return array('data' => $result, 'count' => $count, 'message' => $msg);
	}

	public function getEditViewData($id)
	{
		return $this->getViewViewData($id);
	}

	public function getViewViewData($id)
	{
		$model = $this->model_name;
		$result = $model::find($id);
	
		return $result;
	}

	public static function cleanDatabaseOperation($operations)
	{
		/*
			| Performs database operations with error checking
			| The parameter is an array in which each element is array($obj(Object),$method(String),$arguments(array))
			| The operation performed is $obj->method($arguments)
			| The param is returned only when one of the method call is storeInDatabase(). In case of multiple such methods, the id of final call will be returned
			| This function is especially suited for series of database operation of which failure in one entails rollback of the bunch
		*/
			
		$success = false;
		$msg = '';
		$param = array('id' => 0);
		
		try
		{
			DB::connection()->getPdo()->beginTransaction();

				foreach($operations as $operation)
				{
					$object = $operation[0];
					$method = $operation[1];
					$argument = $operation[2];

					$temp_id = null;
					if($argument == null)
					{
						call_user_func(array($object, $method));
					}
					else
					{
						$temp_id = call_user_func_array(array($object, $method),$argument);	
					}
					
					if ($method == 'storeInDatabase')
					{
						$param['id'] = $temp_id;
					}
					
				}

				$success = true;
				$msg = 'Operation successful';
				//$param['id'] = $id;

			DB::connection()->getPdo()->commit();
		}
		catch(PDOException $e)
		{
			DB::connection()->getPdo()->rollback();
			$success = false;
			$msg = $e->getMessage();
		}

		return [
				'success'=>$success,
				'msg'=>$msg,
				'param'=>$param
				];
	}

	public function checkPermissions($current_user, $module_name = '')
	{
		$return = array('allow_to_view_list' => false, 'allow_to_view' => false, 'allow_to_edit' => false, 'allow_to_create' => false, 'allow_to_delete' => false, 'allow_to_purge' => false);

		if($current_user->role == 'superadmin')
		{
			$return['allow_to_edit'] = $return['allow_to_view_list'] = $return['allow_to_view'] = $return['allow_to_create'] = $return['allow_to_delete'] = $return['allow_to_purge'] = $return['allow_to_view'] = true;
		}
		elseif($module_name != '')
		{
			$group_ids = HelperController::getGroupIdsOfUser($current_user->role, $current_user->id);
			$access_controller = new AccessController;
			$access_permissions = $access_controller->getCurrentPermissions($module_name);

			//TODO: fix this
			if(!$access_permissions)
			{
				$return['allow_to_edit'] = $return['allow_to_view_list'] = $return['allow_to_view'] = $return['allow_to_create'] = $return['allow_to_delete'] = $return['allow_to_purge'] = $return['allow_to_view'] = true;
				return $return;
			}

			if($access_permissions[$module_name.'-list']['all'] == 'yes')
			{
				$return['allow_to_view_list'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-list'][$current_user->role])))
			{
				
				$return['allow_to_view_list'] = true;
			}
			else
			{
				$return['allow_to_view_list'] = false;
			}

			if($access_permissions[$module_name.'-view']['all'] == 'yes')
			{
				$return['allow_to_view'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-view'][$current_user->role])))
			{
				
				$return['allow_to_view'] = true;
			}
			else
			{
				$return['allow_to_view'] = false;
			}

			if($access_permissions[$module_name.'-create-get']['all'] == 'yes' && $access_permissions[$module_name.'-create-post']['all'] == 'yes')
			{
				$return['allow_to_create'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-create-get'][$current_user->role])) > 0 && count(array_intersect($group_ids, $access_permissions[$module_name.'-create-post'][$current_user->role])))
			{
				
				$return['allow_to_create'] = true;
			}
			else
			{
				$return['allow_to_create'] = false;
			}

			if($access_permissions[$module_name.'-edit-get']['all'] == 'yes' && $access_permissions[$module_name.'-edit-post']['all'] == 'yes')
			{
				$return['allow_to_edit'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-edit-get'][$current_user->role])) > 0 && count(array_intersect($group_ids, $access_permissions[$module_name.'-edit-post'][$current_user->role])))
			{
				
				$return['allow_to_edit'] = true;
			}
			else
			{
				$return['allow_to_edit'] = false;
			}

			if($access_permissions[$module_name.'-delete-post']['all'] == 'yes')
			{
				$return['allow_to_delete'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-delete-post'][$current_user->role])))
			{
				$return['allow_to_delete'] = true;
			}
			else
			{
				$return['allow_to_delete'] = false;
			}

			if($access_permissions[$module_name.'-purge-post']['all'] == 'yes')
			{
				$return['allow_to_purge'] = true;
			}
			elseif(count(array_intersect($group_ids, $access_permissions[$module_name.'-purge-post'][$current_user->role])))
			{
				$return['allow_to_purge'] = true;
			}
			else
			{
				$return['allow_to_purge'] = false;
			}
		}
		else
		{
			$return['allow_to_edit'] = $return['allow_to_view_list'] = $return['allow_to_view'] = $return['allow_to_create'] = $return['allow_to_delete'] = $return['allow_to_purge'] = $return['allow_to_view'] = true;
		}

		return $return;
	}

	public static function getLastUpdated($condition = array(), $fields_required = array(), $model_name)
	{
		$con = array();
		foreach($condition as $c)
		{
			$con[] = array($c['field_name'], $c['operator'], $c['compare_value']);
		}

		$data = new $model_name;

		if(count($con))
		{
			foreach($con as $c)
			$data = $data->where($c[0], $c[1], $c[2]);
		}

		$max_updated_time = $data->max('updated_at');

		if(count($fields_required))
		{
			$data = new $model_name;
			if(count($con))
			{
				foreach($con as $c)
				{
					$data = $data->where($c[0], $c[1], $c[2]);	
				}
				
			}
			$fields_required[] = 'updated_at';
			$max_updated_time = $data->where('updated_at', $max_updated_time)->select($fields_required)->first();
		}

		return $max_updated_time;
	}

}

