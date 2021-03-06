<?php

// Generated by Haxe 3.4.4
class sys_db_Manager {
	public function __construct($classval) {
		if(!php_Boot::$skip_constructor) {
		$GLOBALS['%s']->push("sys.db.Manager::new");
		$__hx__spos = $GLOBALS['%s']->length;
		$m = haxe_rtti_Meta::getType($classval)->rtti;
		if($m === null) {
			throw new HException("Missing @rtti for class " . _hx_string_or_null(Type::getClassName($classval)));
		}
		$this->table_infos = haxe_Unserializer::run($m[0]);
		$this->table_name = $this->quoteField($this->table_infos->name);
		$this->table_keys = $this->table_infos->key;
		$this->class_proto = $classval;
		$GLOBALS['%s']->pop();
	}}
	public $table_infos;
	public $table_name;
	public $table_keys;
	public $class_proto;
	public function all($lock = null) {
		$GLOBALS['%s']->push("sys.db.Manager::all");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->unsafeObjects("SELECT * FROM " . _hx_string_or_null($this->table_name), $lock);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function doUpdateCache($x, $name, $v) {
		$GLOBALS['%s']->push("sys.db.Manager::doUpdateCache");
		$__hx__spos = $GLOBALS['%s']->length;
		$cache = Reflect::field($x, "cache_" . _hx_string_or_null($name));
		if($cache === null) {
			$GLOBALS['%s']->pop();
			return $v;
		}
		$v1 = $this->doSerialize($name, $cache->v);
		{
			$GLOBALS['%s']->pop();
			return $v1;
		}
		$GLOBALS['%s']->pop();
	}
	public function doInsert($x) {
		$GLOBALS['%s']->push("sys.db.Manager::doInsert");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->unmake($x);
		$s = new StringBuf();
		$fields = new HList();
		$values = new HList();
		$cache = Reflect::field($x, "__cache__");
		if($cache === null) {
			$cache = _hx_anonymous(array());
			$value = $cache;
			$x->{"__cache__"} = $value;
		}
		{
			$_g = 0;
			$_g1 = $this->table_infos->fields;
			while($_g < $_g1->length) {
				$f = $_g1[$_g];
				$_g = $_g + 1;
				$name = $f->name;
				$fieldName = sys_db_Manager::getFieldName($f);
				$v = Reflect::field($x, $fieldName);
				if($v !== null) {
					$fields->add($this->quoteField($name));
					{
						$_g2 = $f->t;
						if($_g2->index === 30) {
							$v = $this->doUpdateCache($x, $name, $v);
						}
						unset($_g2);
					}
					$values->add($v);
				} else {
					if(!$f->isNull) {
						$_g21 = $f->t;
						switch($_g21->index) {
						case 1:case 3:case 5:case 6:case 7:case 23:case 24:case 25:case 26:case 27:case 28:case 29:case 31:{
							$x->{$fieldName} = 0;
						}break;
						case 8:{
							$x->{$fieldName} = false;
						}break;
						case 10:case 11:case 12:{
						}break;
						case 9:case 13:case 14:case 15:case 21:{
							$x->{$fieldName} = "";
						}break;
						case 16:case 17:case 18:case 19:case 22:{
							$value1 = haxe_io_Bytes::alloc(0);
							$x->{$fieldName} = $value1;
						}break;
						case 0:case 2:case 4:case 20:case 30:case 32:case 33:{
						}break;
						}
						unset($_g21);
					}
				}
				$cache->{$name} = $v;
				unset($v,$name,$fieldName,$f);
			}
		}
		$s->add("INSERT INTO ");
		$s->add($this->table_name);
		$tmp = null;
		if($fields->length <= 0) {
			$tmp = sys_db_Manager::$cnx->dbName() !== "SQLite";
		} else {
			$tmp = true;
		}
		if($tmp) {
			$s->add(" (");
			$s->add($fields->join(","));
			$s->add(") VALUES (");
			$first = true;
			{
				$v1 = $values->iterator();
				while($v1->hasNext()) {
					$v2 = $v1->next();
					if($first) {
						$first = false;
					} else {
						$s->add(", ");
					}
					$this->getCnx()->addValue($s, $v2);
					unset($v2);
				}
			}
			$s->add(")");
		} else {
			$s->add(" DEFAULT VALUES");
		}
		$this->unsafeExecute($s->b);
		$x->_lock = true;
		$tmp1 = null;
		if($this->table_keys->length === 1) {
			$tmp1 = Reflect::field($x, $this->table_keys[0]) === null;
		} else {
			$tmp1 = false;
		}
		if($tmp1) {
			$field = $this->table_keys[0];
			$value2 = $this->getCnx()->lastInsertId();
			$x->{$field} = $value2;
		}
		$this->addToCache($x);
		$GLOBALS['%s']->pop();
	}
	public function doUpdate($x) {
		$GLOBALS['%s']->push("sys.db.Manager::doUpdate");
		$__hx__spos = $GLOBALS['%s']->length;
		if(!$x->_lock) {
			throw new HException("Cannot update a not locked object");
		}
		$upd = $this->getUpdateStatement($x);
		if($upd === null) {
			$GLOBALS['%s']->pop();
			return;
		}
		$this->unsafeExecute($upd);
		$GLOBALS['%s']->pop();
	}
	public function getUpdateStatement($x) {
		$GLOBALS['%s']->push("sys.db.Manager::getUpdateStatement");
		$__hx__spos = $GLOBALS['%s']->length;
		$this->unmake($x);
		$s = new StringBuf();
		$s->add("UPDATE ");
		$s->add($this->table_name);
		$s->add(" SET ");
		$cache = Reflect::field($x, "__cache__");
		$mod = false;
		{
			$_g = 0;
			$_g1 = $this->table_infos->fields;
			while($_g < $_g1->length) {
				$f = $_g1[$_g];
				$_g = $_g + 1;
				if($this->table_keys->indexOf($f->name, null) >= 0) {
					continue;
				}
				$name = $f->name;
				$fieldName = sys_db_Manager::getFieldName($f);
				$v = Reflect::field($x, $fieldName);
				$vc = Reflect::field($cache, $name);
				$tmp = null;
				if($cache !== null) {
					$tmp = !_hx_equal($v, $vc);
				} else {
					$tmp = true;
				}
				if($tmp) {
					{
						$_g2 = $f->t;
						switch($_g2->index) {
						case 16:case 17:case 18:case 19:case 22:{
							$a = $v;
							$b = $vc;
							$tmp1 = null;
							if($a !== $b) {
								$tmp2 = null;
								if($a !== null) {
									$tmp2 = $b === null;
								} else {
									$tmp2 = true;
								}
								if(!$tmp2) {
									$tmp1 = $a->compare($b) !== 0;
								} else {
									$tmp1 = true;
								}
							} else {
								$tmp1 = false;
							}
							if(!$tmp1) {
								continue 2;
							}
						}break;
						case 30:{
							$v = $this->doUpdateCache($x, $name, $v);
							$a1 = $v;
							$b1 = $vc;
							$tmp3 = null;
							if($a1 !== $b1) {
								$tmp4 = null;
								if($a1 !== null) {
									$tmp4 = $b1 === null;
								} else {
									$tmp4 = true;
								}
								if(!$tmp4) {
									$tmp3 = $a1->compare($b1) !== 0;
								} else {
									$tmp3 = true;
								}
							} else {
								$tmp3 = false;
							}
							if(!$tmp3) {
								continue 2;
							}
						}break;
						default:{
						}break;
						}
						unset($_g2);
					}
					if($mod) {
						$s->add(", ");
					} else {
						$mod = true;
					}
					$s->add($this->quoteField($name));
					$s->add(" = ");
					$this->getCnx()->addValue($s, $v);
					if($cache !== null) {
						$cache->{$name} = $v;
					}
				}
				unset($vc,$v,$tmp,$name,$fieldName,$f);
			}
		}
		if(!$mod) {
			$GLOBALS['%s']->pop();
			return null;
		}
		$s->add(" WHERE ");
		$this->addKeys($s, $x);
		{
			$tmp = $s->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function doDelete($x) {
		$GLOBALS['%s']->push("sys.db.Manager::doDelete");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new StringBuf();
		$s->add("DELETE FROM ");
		$s->add($this->table_name);
		$s->add(" WHERE ");
		$this->addKeys($s, $x);
		$this->unsafeExecute($s->b);
		$this->removeFromCache($x);
		$GLOBALS['%s']->pop();
	}
	public function doLock($i) {
		$GLOBALS['%s']->push("sys.db.Manager::doLock");
		$__hx__spos = $GLOBALS['%s']->length;
		if($i->_lock) {
			$GLOBALS['%s']->pop();
			return;
		}
		$s = new StringBuf();
		$s->add("SELECT * FROM ");
		$s->add($this->table_name);
		$s->add(" WHERE ");
		$this->addKeys($s, $i);
		if((is_object($_t = $this->unsafeObject($s->b, true)) && ($_t instanceof Enum) ? $_t != $i : !_hx_equal($_t, $i))) {
			throw new HException("Could not lock object (was deleted ?); try restarting transaction");
		}
		$GLOBALS['%s']->pop();
	}
	public function objectToString($it) {
		$GLOBALS['%s']->push("sys.db.Manager::objectToString");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new StringBuf();
		$s->add($this->table_name);
		if($this->table_keys->length === 1) {
			$s->add("#");
			$s->add(Reflect::field($it, $this->table_keys[0]));
		} else {
			$s->add("(");
			$first = true;
			{
				$_g = 0;
				$_g1 = $this->table_keys;
				while($_g < $_g1->length) {
					$f = $_g1[$_g];
					$_g = $_g + 1;
					if($first) {
						$first = false;
					} else {
						$s->add(",");
					}
					$s->add($this->quoteField($f));
					$s->add(":");
					$s->add(Reflect::field($it, $f));
					unset($f);
				}
			}
			$s->add(")");
		}
		{
			$tmp = $s->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function doSerialize($field, $v) {
		$GLOBALS['%s']->push("sys.db.Manager::doSerialize");
		$__hx__spos = $GLOBALS['%s']->length;
		$s = new haxe_Serializer();
		$s->useEnumIndex = true;
		$s->serialize($v);
		$str = $s->toString();
		{
			$tmp = haxe_io_Bytes::ofString($str);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function normalizeCache($x) {
		$GLOBALS['%s']->push("sys.db.Manager::normalizeCache");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = 0;
		$_g1 = Reflect::fields($x);
		while($_g < $_g1->length) {
			$f = $_g1[$_g];
			$_g = $_g + 1;
			$val = Reflect::field($x, $f);
			$info = $this->table_infos->hfields->get($f);
			if($info !== null) {
				if($val !== null) {
					$_g2 = $info->t;
					switch($_g2->index) {
					case 7:{
						if(Std::is($val, _hx_qtype("String"))) {
							$val = Std::parseFloat($val);
						}
					}break;
					case 8:{
						if(!Std::is($val, _hx_qtype("Bool"))) {
							if(Std::is($val, _hx_qtype("Int"))) {
								$val = !_hx_equal($val, 0);
							} else {
								if(Std::is($val, _hx_qtype("String"))) {
									$_g21 = _hx_string_call($val, "toLowerCase", array());
									switch($_g21) {
									case "1":case "true":{
										$val = true;
									}break;
									case "0":case "false":{
										$val = false;
									}break;
									}
								}
							}
						}
					}break;
					case 10:case 11:{
						if(!Std::is($val, _hx_qtype("Date"))) {
							if(Std::is($val, _hx_qtype("Float"))) {
								$val = Date::fromTime($val);
							} else {
								$v = Std::string($val) . "";
								$index = _hx_index_of($v, ".", null);
								if($index >= 0) {
									$v = _hx_substr($v, 0, $index);
								}
								$val = Date::fromString($v);
							}
						}
					}break;
					case 9:case 13:case 14:case 15:{
						if(!Std::is($val, _hx_qtype("String"))) {
							$val = Std::string($val) . "";
						}
					}break;
					case 16:case 17:case 18:case 19:case 30:{
						if(Std::is($val, _hx_qtype("String"))) {
							$val = haxe_io_Bytes::ofString($val);
						}
					}break;
					default:{
					}break;
					}
					unset($_g2);
				}
				$x->{$f} = $val;
			}
			unset($val,$info,$f);
		}
		$GLOBALS['%s']->pop();
	}
	public function cacheObject($x, $lock) {
		$GLOBALS['%s']->push("sys.db.Manager::cacheObject");
		$__hx__spos = $GLOBALS['%s']->length;
		$o = Type::createEmptyInstance($this->class_proto);
		$o->_manager = $this;
		$this->normalizeCache($x);
		{
			$_g = 0;
			$_g1 = Reflect::fields($x);
			while($_g < $_g1->length) {
				$f = $_g1[$_g];
				$_g = $_g + 1;
				$val = Reflect::field($x, $f);
				$info = $this->table_infos->hfields->get($f);
				if($info !== null) {
					$fieldName = sys_db_Manager::getFieldName($info);
					$o->{$fieldName} = $val;
					unset($fieldName);
				}
				unset($val,$info,$f);
			}
		}
		$o->{"__cache__"} = $x;
		$this->addToCache($o);
		$o->_lock = $lock;
		{
			$GLOBALS['%s']->pop();
			return $o;
		}
		$GLOBALS['%s']->pop();
	}
	public function make($x) {
		$GLOBALS['%s']->push("sys.db.Manager::make");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}
	public function unmake($x) {
		$GLOBALS['%s']->push("sys.db.Manager::unmake");
		$__hx__spos = $GLOBALS['%s']->length;
		$GLOBALS['%s']->pop();
	}
	public function quoteField($f) {
		$GLOBALS['%s']->push("sys.db.Manager::quoteField");
		$__hx__spos = $GLOBALS['%s']->length;
		$tmp = sys_db_Manager::$KEYWORDS;
		if($tmp->exists(strtolower($f))) {
			$tmp2 = "`" . _hx_string_or_null($f) . "`";
			$GLOBALS['%s']->pop();
			return $tmp2;
		} else {
			$GLOBALS['%s']->pop();
			return $f;
		}
		$GLOBALS['%s']->pop();
	}
	public function addKeys($s, $x) {
		$GLOBALS['%s']->push("sys.db.Manager::addKeys");
		$__hx__spos = $GLOBALS['%s']->length;
		$first = true;
		{
			$_g = 0;
			$_g1 = $this->table_keys;
			while($_g < $_g1->length) {
				$k = $_g1[$_g];
				$_g = $_g + 1;
				if($first) {
					$first = false;
				} else {
					$s->add(" AND ");
				}
				$s->add($this->quoteField($k));
				$s->add(" = ");
				$f = Reflect::field($x, $k);
				if($f === null) {
					throw new HException("Missing key " . _hx_string_or_null($k));
				}
				$this->getCnx()->addValue($s, $f);
				unset($k,$f);
			}
		}
		$GLOBALS['%s']->pop();
	}
	public function unsafeExecute($sql) {
		$GLOBALS['%s']->push("sys.db.Manager::unsafeExecute");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->getCnx()->request($sql);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function unsafeObject($sql, $lock) {
		$GLOBALS['%s']->push("sys.db.Manager::unsafeObject");
		$__hx__spos = $GLOBALS['%s']->length;
		if($lock !== false) {
			$lock = true;
			$sql = _hx_string_or_null($sql) . _hx_string_or_null($this->getLockMode());
		}
		$r = $this->unsafeExecute($sql);
		$r1 = null;
		if($r->hasNext()) {
			$r1 = $r->next();
		} else {
			$r1 = null;
		}
		if($r1 === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		$this->normalizeCache($r1);
		$c = $this->getFromCache($r1, $lock);
		if($c !== null) {
			$GLOBALS['%s']->pop();
			return $c;
		}
		$r1 = $this->cacheObject($r1, $lock);
		$this->make($r1);
		{
			$GLOBALS['%s']->pop();
			return $r1;
		}
		$GLOBALS['%s']->pop();
	}
	public function unsafeObjects($sql, $lock) {
		$GLOBALS['%s']->push("sys.db.Manager::unsafeObjects");
		$__hx__spos = $GLOBALS['%s']->length;
		if($lock !== false) {
			$lock = true;
			$sql = _hx_string_or_null($sql) . _hx_string_or_null($this->getLockMode());
		}
		$l = $this->unsafeExecute($sql)->results();
		$l2 = new HList();
		{
			$x = $l->iterator();
			while($x->hasNext()) {
				$x1 = $x->next();
				$this->normalizeCache($x1);
				$c = $this->getFromCache($x1, $lock);
				if($c !== null) {
					$l2->add($c);
				} else {
					$x1 = $this->cacheObject($x1, $lock);
					$this->make($x1);
					$l2->add($x1);
				}
				unset($x1,$c);
			}
		}
		{
			$GLOBALS['%s']->pop();
			return $l2;
		}
		$GLOBALS['%s']->pop();
	}
	public function unsafeGet($id, $lock = null) {
		$GLOBALS['%s']->push("sys.db.Manager::unsafeGet");
		$__hx__spos = $GLOBALS['%s']->length;
		if($lock === null) {
			$lock = true;
		}
		if($this->table_keys->length !== 1) {
			throw new HException("Invalid number of keys");
		}
		if($id === null) {
			$GLOBALS['%s']->pop();
			return null;
		}
		$x = Std::string($id);
		$x1 = $this->getFromCacheKey(_hx_string_or_null($x) . _hx_string_or_null($this->table_name));
		$tmp = null;
		if($x1 !== null) {
			if(!(!$lock)) {
				$tmp = _hx_field($x1, "_lock");
			} else {
				$tmp = true;
			}
		} else {
			$tmp = false;
		}
		if($tmp) {
			$GLOBALS['%s']->pop();
			return $x1;
		}
		$s = new StringBuf();
		$s->add("SELECT * FROM ");
		$s->add($this->table_name);
		$s->add(" WHERE ");
		$s->add($this->quoteField($this->table_keys[0]));
		$s->add(" = ");
		$this->getCnx()->addValue($s, $id);
		{
			$tmp2 = $this->unsafeObject($s->b, $lock);
			$GLOBALS['%s']->pop();
			return $tmp2;
		}
		$GLOBALS['%s']->pop();
	}
	public function dbInfos() {
		$GLOBALS['%s']->push("sys.db.Manager::dbInfos");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = $this->table_infos;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getCnx() {
		$GLOBALS['%s']->push("sys.db.Manager::getCnx");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = sys_db_Manager::$cnx;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getLockMode() {
		$GLOBALS['%s']->push("sys.db.Manager::getLockMode");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = sys_db_Manager::$lockMode;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function initRelation($r) {
		$GLOBALS['%s']->push("sys.db.Manager::initRelation");
		$__hx__spos = $GLOBALS['%s']->length;
		$spod = Type::resolveClass($r->type);
		if($spod === null) {
			throw new HException("Missing spod type " . _hx_string_or_null($r->type));
		}
		$manager = _hx_field($spod, "manager");
		$hprop = "__" . _hx_string_or_null($r->prop);
		$hkey = $r->key;
		$lock = $r->lock;
		$tmp = null;
		if($manager !== null) {
			$tmp = $manager->table_keys === null;
		} else {
			$tmp = true;
		}
		if($tmp) {
			throw new HException("Invalid manager for relation " . _hx_string_or_null($this->table_name) . ":" . _hx_string_or_null($r->prop));
		}
		if($manager->table_keys->length !== 1) {
			throw new HException("Relation " . _hx_string_or_null($r->prop) . "(" . _hx_string_or_null($r->key) . ") on a multiple key table");
		}
		$GLOBALS['%s']->pop();
	}
	public function makeCacheKey($x) {
		$GLOBALS['%s']->push("sys.db.Manager::makeCacheKey");
		$__hx__spos = $GLOBALS['%s']->length;
		if($this->table_keys->length === 1) {
			$k = Reflect::field($x, $this->table_keys[0]);
			if($k === null) {
				throw new HException("Missing key " . _hx_string_or_null($this->table_keys[0]));
			}
			$tmp = Std::string($k);
			{
				$tmp2 = _hx_string_or_null($tmp) . _hx_string_or_null($this->table_name);
				$GLOBALS['%s']->pop();
				return $tmp2;
			}
		}
		$s = new StringBuf();
		{
			$_g = 0;
			$_g1 = $this->table_keys;
			while($_g < $_g1->length) {
				$k1 = $_g1[$_g];
				$_g = $_g + 1;
				$v = Reflect::field($x, $k1);
				if($k1 === null) {
					throw new HException("Missing key " . _hx_string_or_null($k1));
				}
				$s->add($v);
				$s->add("#");
				unset($v,$k1);
			}
		}
		$s->add($this->table_name);
		{
			$tmp = $s->b;
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function addToCache($x) {
		$GLOBALS['%s']->push("sys.db.Manager::addToCache");
		$__hx__spos = $GLOBALS['%s']->length;
		$tmp = sys_db_Manager::$object_cache;
		$tmp->set($this->makeCacheKey($x), $x);
		$GLOBALS['%s']->pop();
	}
	public function removeFromCache($x) {
		$GLOBALS['%s']->push("sys.db.Manager::removeFromCache");
		$__hx__spos = $GLOBALS['%s']->length;
		$tmp = sys_db_Manager::$object_cache;
		$tmp->remove($this->makeCacheKey($x));
		$GLOBALS['%s']->pop();
	}
	public function getFromCacheKey($key) {
		$GLOBALS['%s']->push("sys.db.Manager::getFromCacheKey");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = sys_db_Manager::$object_cache->get($key);
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	public function getFromCache($x, $lock) {
		$GLOBALS['%s']->push("sys.db.Manager::getFromCache");
		$__hx__spos = $GLOBALS['%s']->length;
		$c = sys_db_Manager::$object_cache;
		$c1 = $c->get($this->makeCacheKey($x));
		$tmp = null;
		$tmp1 = null;
		if($c1 !== null) {
			$tmp1 = $lock;
		} else {
			$tmp1 = false;
		}
		if($tmp1) {
			$tmp = !_hx_field($c1, "_lock");
		} else {
			$tmp = false;
		}
		if($tmp) {
			{
				$_g = 0;
				$_g1 = Reflect::fields($c1);
				while($_g < $_g1->length) {
					$f = $_g1[$_g];
					$_g = $_g + 1;
					Reflect::deleteField($c1, $f);
					unset($f);
				}
			}
			{
				$_g2 = 0;
				$_g11 = $this->table_infos->fields;
				while($_g2 < $_g11->length) {
					$f1 = $_g11[$_g2];
					$_g2 = $_g2 + 1;
					$name = $f1->name;
					$fieldName = sys_db_Manager::getFieldName($f1);
					{
						$value = Reflect::field($x, $name);
						$c1->{$fieldName} = $value;
						unset($value);
					}
					unset($name,$fieldName,$f1);
				}
			}
			$c1->_lock = true;
			$c1->_manager = $this;
			$c1->{"__cache__"} = $x;
			$this->make($c1);
		}
		{
			$GLOBALS['%s']->pop();
			return $c1;
		}
		$GLOBALS['%s']->pop();
	}
	public function __call($m, $a) {
		if(isset($this->$m) && is_callable($this->$m))
			return call_user_func_array($this->$m, $a);
		else if(isset($this->__dynamics[$m]) && is_callable($this->__dynamics[$m]))
			return call_user_func_array($this->__dynamics[$m], $a);
		else if('toString' == $m)
			return $this->__toString();
		else
			throw new HException('Unable to call <'.$m.'>');
	}
	static $cnx;
	static $lockMode;
	static $object_cache;
	static $init_list;
	static $KEYWORDS;
	static function set_cnx($c) {
		$GLOBALS['%s']->push("sys.db.Manager::set_cnx");
		$__hx__spos = $GLOBALS['%s']->length;
		sys_db_Manager::$cnx = $c;
		$tmp = null;
		$tmp1 = null;
		if($c !== null) {
			$tmp1 = $c->dbName() === "MySQL";
		} else {
			$tmp1 = false;
		}
		if($tmp1) {
			$tmp = " FOR UPDATE";
		} else {
			$tmp = "";
		}
		sys_db_Manager::$lockMode = $tmp;
		{
			$GLOBALS['%s']->pop();
			return $c;
		}
		$GLOBALS['%s']->pop();
	}
	static function getFieldName($field) {
		$GLOBALS['%s']->push("sys.db.Manager::getFieldName");
		$__hx__spos = $GLOBALS['%s']->length;
		$_g = $field->t;
		switch($_g->index) {
		case 30:case 31:{
			$tmp = "data_" . _hx_string_or_null($field->name);
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		default:{
			$tmp = $field->name;
			$GLOBALS['%s']->pop();
			return $tmp;
		}break;
		}
		$GLOBALS['%s']->pop();
	}
	static function initialize() {
		$GLOBALS['%s']->push("sys.db.Manager::initialize");
		$__hx__spos = $GLOBALS['%s']->length;
		$l = sys_db_Manager::$init_list;
		sys_db_Manager::$init_list = new HList();
		{
			$m = $l->iterator();
			while($m->hasNext()) {
				$m1 = $m->next();
				$_g = 0;
				$_g1 = $m1->table_infos->relations;
				while($_g < $_g1->length) {
					$r = $_g1[$_g];
					$_g = $_g + 1;
					$m1->initRelation($r);
					unset($r);
				}
				unset($m1,$_g1,$_g);
			}
		}
		$GLOBALS['%s']->pop();
	}
	static function cleanup() {
		$GLOBALS['%s']->push("sys.db.Manager::cleanup");
		$__hx__spos = $GLOBALS['%s']->length;
		sys_db_Manager::$object_cache = new haxe_ds_StringMap();
		$GLOBALS['%s']->pop();
	}
	static function __depends() {
		$GLOBALS['%s']->push("sys.db.Manager::__depends");
		$__hx__spos = $GLOBALS['%s']->length;
		{
			$tmp = haxe_io_Bytes::alloc(0)->toString();
			$GLOBALS['%s']->pop();
			return $tmp;
		}
		$GLOBALS['%s']->pop();
	}
	static $__properties__ = array("set_cnx" => "set_cnx");
	function __toString() { return 'sys.db.Manager'; }
}
sys_db_Manager::$object_cache = new haxe_ds_StringMap();
sys_db_Manager::$init_list = new HList();
sys_db_Manager::$KEYWORDS = sys_db_Manager_0();
function sys_db_Manager_0() {
	{
		$h = new haxe_ds_StringMap();
		{
			$_g = 0;
			$_g1 = _hx_explode("|", "ADD|ALL|ALTER|ANALYZE|AND|AS|ASC|ASENSITIVE|BEFORE|BETWEEN|BIGINT|BINARY|BLOB|BOTH|BY|CALL|CASCADE|CASE|CHANGE|CHAR|CHARACTER|CHECK|COLLATE|COLUMN|CONDITION|CONSTRAINT|CONTINUE|CONVERT|CREATE|CROSS|CURRENT_DATE|CURRENT_TIME|CURRENT_TIMESTAMP|CURRENT_USER|CURSOR|DATABASE|DATABASES|DAY_HOUR|DAY_MICROSECOND|DAY_MINUTE|DAY_SECOND|DEC|DECIMAL|DECLARE|DEFAULT|DELAYED|DELETE|DESC|DESCRIBE|DETERMINISTIC|DISTINCT|DISTINCTROW|DIV|DOUBLE|DROP|DUAL|EACH|ELSE|ELSEIF|ENCLOSED|ESCAPED|EXISTS|EXIT|EXPLAIN|FALSE|FETCH|FLOAT|FLOAT4|FLOAT8|FOR|FORCE|FOREIGN|FROM|FULLTEXT|GRANT|GROUP|HAVING|HIGH_PRIORITY|HOUR_MICROSECOND|HOUR_MINUTE|HOUR_SECOND|IF|IGNORE|IN|INDEX|INFILE|INNER|INOUT|INSENSITIVE|INSERT|INT|INT1|INT2|INT3|INT4|INT8|INTEGER|INTERVAL|INTO|IS|ITERATE|JOIN|KEY|KEYS|KILL|LEADING|LEAVE|LEFT|LIKE|LIMIT|LINES|LOAD|LOCALTIME|LOCALTIMESTAMP|LOCK|LONG|LONGBLOB|LONGTEXT|LOOP|LOW_PRIORITY|MATCH|MEDIUMBLOB|MEDIUMINT|MEDIUMTEXT|MIDDLEINT|MINUTE_MICROSECOND|MINUTE_SECOND|MOD|MODIFIES|NATURAL|NOT|NO_WRITE_TO_BINLOG|NULL|NUMERIC|ON|OPTIMIZE|OPTION|OPTIONALLY|OR|ORDER|OUT|OUTER|OUTFILE|PRECISION|PRIMARY|PROCEDURE|PURGE|READ|READS|REAL|REFERENCES|REGEXP|RELEASE|RENAME|REPEAT|REPLACE|REQUIRE|RESTRICT|RETURN|REVOKE|RIGHT|RLIKE|SCHEMA|SCHEMAS|SECOND_MICROSECOND|SELECT|SENSITIVE|SEPARATOR|SET|SHOW|SMALLINT|SONAME|SPATIAL|SPECIFIC|SQL|SQLEXCEPTION|SQLSTATE|SQLWARNING|SQL_BIG_RESULT|SQL_CALC_FOUND_ROWS|SQL_SMALL_RESULT|SSL|STARTING|STRAIGHT_JOIN|TABLE|TERMINATED|THEN|TINYBLOB|TINYINT|TINYTEXT|TO|TRAILING|TRIGGER|TRUE|UNDO|UNION|UNIQUE|UNLOCK|UNSIGNED|UPDATE|USAGE|USE|USING|UTC_DATE|UTC_TIME|UTC_TIMESTAMP|VALUES|VARBINARY|VARCHAR|VARCHARACTER|VARYING|WHEN|WHERE|WHILE|WITH|WRITE|XOR|YEAR_MONTH|ZEROFILL|ASENSITIVE|CALL|CONDITION|CONNECTION|CONTINUE|CURSOR|DECLARE|DETERMINISTIC|EACH|ELSEIF|EXIT|FETCH|GOTO|INOUT|INSENSITIVE|ITERATE|LABEL|LEAVE|LOOP|MODIFIES|OUT|READS|RELEASE|REPEAT|RETURN|SCHEMA|SCHEMAS|SENSITIVE|SPECIFIC|SQL|SQLEXCEPTION|SQLSTATE|SQLWARNING|TRIGGER|UNDO|UPGRADE|WHILE");
			while($_g < $_g1->length) {
				$k = $_g1[$_g];
				$_g = $_g + 1;
				$h->set(strtolower($k), true);
				unset($k);
			}
		}
		return $h;
	}
}
