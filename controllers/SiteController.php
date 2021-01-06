<?php

namespace app\controllers;

use app\models\Armors;
use app\models\ArmorsSearch;
use app\models\Auth;
use app\models\ExportArmorForm;
use app\models\Godroll;
use app\models\GodrollSearch;
use app\models\SyncForm;
use app\models\Usage;
use app\models\User;
use app\models\WeaponPerks;
use app\models\Weapons;
use app\models\WeaponsSearch;
use Yii;
use yii\base\Exception;
use yii\filters\AccessControl;
use yii\httpclient\Client;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;

class SiteController extends Controller
{
	public $godroll_attributes = ['Sight_Barrel', 'Mag_Perk', 'Perk_1', 'Perk_2', 'Masterwork'];

	public $current_season = 'arrival';

	public $seasons = [
		'outlaw' => 4,
		'forge' => 5,
		'opulent' => 7,
		'undying' => 8,
		'dawn' => 9,
		'worthy' => 10,
		'arrival' => 11,
	];

	public $power_limit = [
		'arrival' => 1060
	];

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::className(),
				'only' => ['logout'],
				'rules' => [
					[
						'actions' => ['logout'],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::className(),
				'actions' => [
					'logout' => ['post'],
				],
			],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function actions()
	{
		return [
			'error' => [
				'class' => 'yii\web\ErrorAction',
			],
			'captcha' => [
				'class' => 'yii\captcha\CaptchaAction',
				'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
			],
			'auth' => [
				'class' => 'yii\authclient\AuthAction',
				'successCallback' => [$this, 'onAuthSuccess'],
			],
		];
	}

	/**
	 * Weapon
	 *
	 * @return string
	 */
	public function actionIndex()
	{

		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();
			$searchModel = new WeaponsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

			$perks_count = (new \yii\db\Query())
				->select(['weapon_perks.name'])
				->distinct()
				->from('weapon_perks')
				->innerJoin('weapons', "`Id`=`weapon_id`")
				->where(['weapons.user_id' => Yii::$app->user->id])->count();
		} else {
			$model = false;
			$searchModel = false;
			$dataProvider = false;
			$perks_count = 0;
		}


		return $this->render(
			'index',
			[
				'model' => $model,
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
				'perks_count' => $perks_count
			]
		);
	}

	/**
	 * Armor
	 *
	 * @return string
	 */
	public function actionArmor()
	{
		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();
			$export_model = new ExportArmorForm();
			$searchModel = new ArmorsSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
		} else {
			$model = false;
			$export_model = false;
			$searchModel = false;
			$dataProvider = false;
		}

		return $this->render(
			'armor',
			[
				'model' => $model,
				'export_model' => $export_model,
				'searchModel' => $searchModel,
				'dataProvider' => $dataProvider,
			]
		);
	}

	/**
	 * Top perks
	 *
	 * @return string
	 */
	public function actionPerks()
	{
		$data = [];
		if (!Yii::$app->user->isGuest) {
			ini_set('memory_limit', '6048M');
			ini_set('max_execution_time', 0);

			$godrolls = Godroll::find()->all();

			foreach ($godrolls as $godroll) {
				$cur_type = $godroll->wtype . ' - ' . $godroll->rpm;
				if (!empty($cur_type)) {
					if (!isset($data[$cur_type])) {
						$data[$cur_type] = [];
					}

					foreach ($this->godroll_attributes as $gattr) {
						if (!isset($data[$cur_type][$gattr])) {
							$data[$cur_type][$gattr] = [
								'pve' => [
									'count' => 0
								],
								'pvp' => [
									'count' => 0
								]
							];
						}

						$godroll_arr = mb_split('/', $godroll->$gattr);
						if (count($godroll_arr) == 1) {
							$godroll_arr = mb_split(',', $godroll->$gattr);
						}

						foreach ($godroll_arr as $godroll_atr) {
							$godroll_atr = trim($godroll_atr);
							if (!isset($data[$cur_type][$gattr][$godroll->Type][$godroll_atr])) {
								$data[$cur_type][$gattr][$godroll->Type][$godroll_atr] = 0;
							}
							$data[$cur_type][$gattr][$godroll->Type][$godroll_atr]++;
							$data[$cur_type][$gattr][$godroll->Type]['count']++;
						}
					}
				}
			}
		}

		return $this->render(
			'perks',
			[
				'data' => $data,
				'godroll_attributes' => $this->godroll_attributes
			]
		);
	}

	public function actionGodrolls()
	{
		$weapon_types = [];
		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();

			$searchModel = new GodrollSearch();
			$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

			$wtype_array = (new \yii\db\Query())
				->select(['godroll.wtype'])
				->distinct()
				->from('godroll')->all();
			foreach ($wtype_array as $wtype) {
				if (!is_null($wtype['wtype'])) {
					$weapon_types[$wtype['wtype']] = $wtype['wtype'];
				} else {
					$weapon_types[-1] = '';
				}
			}
		} else {
			$model = null;
			$searchModel = null;
			$dataProvider = null;
		}


		return $this->render(
			'godrolls', [
			'model' => $model,
			'searchModel' => $searchModel,
			'dataProvider' => $dataProvider,
			'weapon_types' => $weapon_types
		]);
	}


	protected function get_weapons_type(&$res, &$processed = 0, $page = 1)
	{
		if ($page > 15) return;
		$http_client = new Client();
		$response = $http_client->createRequest()
			->setMethod('GET')
			->setUrl('https://api.tracker.gg/api/v1/destiny-2/db/items?categories=1&tiers=5&page=' . $page)
			->send();

		//447667954
		$rpm_variants = [
			'4284893193',// weapon - RPM
			'447667954',// bow - Draw Time
			'2961396640' // fusions - Charge Time
		];
		if ($response->isOk && isset($response->data['data']) && isset($response->data['data']['items']) && isset($response->data['data']['total'])) {
			$total = $response->data['data']['total'];
			$processed += $response->data['data']['count'];
			foreach ($response->data['data']['items'] as $weapon) {
				$rpm = null;
				foreach ($weapon['stats'] as $ws) {
					if (in_array($ws['hash'], $rpm_variants)) {
						$rpm = $ws['value'];
						break;
					}
				}
				$res[strtolower($weapon['name'])] = ['type' => $weapon['itemType']['name'], 'rounds' => $rpm];
			}

			if ($total > $processed) {
				$page += 1;
				self::get_weapons_type($res, $processed, $page);
			}
		}
	}

	protected function get_godroll_count($weapon_model, $weapon, $godroll_model, $perks_count, $skip_perks)
	{
		$godroll_count = 0;
		if (trim($godroll_model->Masterwork) == trim($weapon_model->Masterwork_Type)) {
			$godroll_count++;
		}

		for ($i = 0; $i < $perks_count; $i++) {
			$current_perk = 'Perks ' . $i;
			$perk = str_replace(['*', '"'], '', $weapon[$current_perk]);

			if (in_array($perk, $skip_perks)) {
				$perk = '';
			}

			if (!empty($perk)) {
				$perk = trim($perk);

				foreach ($this->godroll_attributes as $gatt) {
					$godroll_arr = mb_split('/', $godroll_model->$gatt);
					if (count($godroll_arr) == 1) {
						$godroll_arr = mb_split(',', $godroll_model->$gatt);
					}

					foreach ($godroll_arr as $godroll_atr) {
						if ($perk == trim($godroll_atr)) {
							$godroll_count++;
						}
					}
				}
			}
		}

		return $godroll_count;
	}

	/**
	 * Weapon sync
	 */
	public function actionSyncWeapons()
	{
		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();
			if (Yii::$app->request->isPost) {
				$model->csv_file = UploadedFile::getInstance($model, 'csv_file');

				if ($model->validate()) {
					Weapons::deleteAll(['user_id' => Yii::$app->user->identity->id]);
					Usage::deleteAll();

					//usage update
					$http_client = new Client();
					$response = $http_client->createRequest()
						->setMethod('GET')
						->setUrl('https://api.tracker.gg/api/v1/destiny-2/db/items/insights?sort=usage&modes=7&tiers=5')
						->send();
					if ($response->isOk && isset($response->data['data'])) {
						$weapon_data = $response->data['data'];
						foreach ($weapon_data as $weapon) {
							$usage_model = Usage::findOne(['Hash' => $weapon['hash']]);
							if (!$usage_model) {
								$usage_model = new Usage();
								$usage_model->Hash = $weapon['hash'];
								$usage_model->Name = $weapon['name'];
							}
							$usage_model->pve_usage = $weapon['percentage']['usage'];
							if (!$usage_model->save()) {
								var_dump($usage_model->getErrors());
								die();
							}
						}
					}

					$response = $http_client->createRequest()
						->setMethod('GET')
						->setUrl('https://api.tracker.gg/api/v1/destiny-2/db/items/insights?sort=usage&modes=69&tiers=5')
						->send();
					if ($response->isOk && isset($response->data['data'])) {
						$weapon_data = $response->data['data'];
						foreach ($weapon_data as $weapon) {
							$usage_model = Usage::findOne(['Hash' => $weapon['hash']]);
							if (!$usage_model) {
								$usage_model = new Usage();
								$usage_model->Hash = $weapon['hash'];
								$usage_model->Name = $weapon['name'];
							}
							$usage_model->pvp_usage = $weapon['percentage']['usage'];
							if (!$usage_model->save()) {
								var_dump($usage_model->getErrors());
								die();
							}
						}
					}

					$csv = array_map('str_getcsv', file($model->csv_file->tempName));
					array_walk($csv, function (&$a) use ($csv) {
						$a = array_combine($csv[0], $a);
					});
					array_shift($csv); # remove column header
					$weapon_attributes = [
						'Id',
						'Name',
						'Hash',
						'Type',
						//'Dmg',
						'Power Limit',
						'Masterwork Type',
						'Masterwork Tier',
					];

					$rpm_variant = [
						'ROF',
						'Charge Time',
						'Draw Time'
					];
					$perks_count = 0;
					$godroll_attributes = ['Sight_Barrel', 'Mag_Perk', 'Perk_1', 'Perk_2'];

					$tiers = [];
					for ($j = 1; $j <= 10; $j++) {
						$tiers[] = "Tier " . $j . " Weapon";
					}
					foreach ($csv as $weapon) {
						if ($weapon['Tier'] != 'Legendary') {
							continue;
						}
						$weapon_model = new Weapons();
						$weapon_model->user_id = Yii::$app->user->identity->id;
						$weapon_model->pve_godrolls = 0;
						$weapon_model->pvp_godrolls = 0;

						foreach ($weapon_attributes as $attribute) {
							$model_attribute = str_replace(' ', '_', $attribute);
							$weapon_attribute = str_replace(['*', '"'], '', $weapon[$attribute]);

							$weapon_model->$model_attribute = $weapon_attribute;
						}

						$rpm = 0;
						foreach ($rpm_variant as $rpm_field) {
							$rmp_tmp = $weapon[$rpm_field];
							if ($rmp_tmp > 0) {
								$rpm = $rmp_tmp;
								break;
							}
						}
						$weapon_model->Rpm = (string)$rpm;

						if ($perks_count == 0) {
							foreach ($weapon as $k => $v) {
								if (mb_substr($k, 0, 5) == 'Perks') {
									$perks_count++;
								}
							}
						}

						if (!$weapon_model->save()) {
							var_dump($weapon_model->getErrors());
							die();
						}

						//weapon god rolls calculation
						$godroll_pve = Godroll::findOne(['Name' => $weapon_model->Name, 'Type' => 'pve']);
						if (!$godroll_pve) {
							$godroll_pve_arr = Godroll::find()->where(['wtype' => $weapon_model->Type, 'rpm' => $weapon_model->Rpm, 'Type' => 'pve'])->all();
							$current_godroll = null;
							$current_godroll_count = 0;
							foreach ($godroll_pve_arr as $godroll_key => $tmp_godroll) {
								$godroll_count = $this->get_godroll_count($weapon_model, $weapon, $tmp_godroll, $perks_count, $tiers);
								if ($godroll_count > $current_godroll_count) $current_godroll = $godroll_key;
							}
							if ($current_godroll) $godroll_pve = $godroll_pve_arr[$current_godroll];
						}

						$godroll_pvp = Godroll::findOne(['Name' => $weapon_model->Name, 'Type' => 'pvp']);
						if (!$godroll_pvp) {
							$godroll_pvp_arr = Godroll::find()->where(['wtype' => $weapon_model->Type, 'rpm' => $weapon_model->Rpm, 'Type' => 'pvp'])->all();
							$current_godroll = null;
							$current_godroll_count = 0;
							foreach ($godroll_pvp_arr as $godroll_key => $tmp_godroll) {
								$godroll_count = $this->get_godroll_count($weapon_model, $weapon, $tmp_godroll, $perks_count, $tiers);
								if ($godroll_count > $current_godroll_count) $current_godroll = $godroll_key;
							}
							if ($current_godroll) $godroll_pvp = $godroll_pvp_arr[$current_godroll];
						}

						//perks save
						if ($godroll_pve) {
							if (trim($godroll_pve->Masterwork) == trim($weapon_model->Masterwork_Type)) {
								$weapon_model->pve_godrolls++;
								$weapon_model->Masterwork_Type_godroll = 'pve';
							}
						}

						if ($godroll_pvp) {
							if (trim($godroll_pvp->Masterwork) == trim($weapon_model->Masterwork_Type)) {
								$weapon_model->pvp_godrolls++;
								if (!empty($weapon_model->Masterwork_Type_godroll)) {
									$weapon_model->Masterwork_Type_godroll .= '|pvp';
								} else {
									$weapon_model->Masterwork_Type_godroll = 'pvp';
								}
							}
						}

						for ($i = 0; $i < $perks_count; $i++) {
							$current_perk = 'Perks ' . $i;
							$perk = str_replace(['*', '"'], '', $weapon[$current_perk]);

							if (in_array($perk, $tiers)) {
								$perk = '';
							}

							if (!empty($perk)) {
								$perk = trim($perk);
								$perk_model = new WeaponPerks();
								$perk_model->weapon_id = $weapon_model->Id;
								$perk_model->name = $current_perk;
								$perk_model->value = $perk;

								/* Perks pve */
								if ($godroll_pve) {
									foreach ($godroll_attributes as $gatt) {
										$godroll_arr = mb_split('/', $godroll_pve->$gatt);
										if (count($godroll_arr) == 1) {
											$godroll_arr = mb_split(',', $godroll_pve->$gatt);
										}

										foreach ($godroll_arr as $godroll_atr) {
											if ($perk == trim($godroll_atr)) {
												$weapon_model->pve_godrolls++;
												$perk_model->godroll = 'pve';
												break 2;
											}
										}
									}
								}

								/* Perks pvp */
								if ($godroll_pvp) {
									foreach ($godroll_attributes as $gatt) {
										$godroll_arr = mb_split('/', $godroll_pvp->$gatt);
										if (count($godroll_arr) == 1) {
											$godroll_arr = mb_split(',', $godroll_pvp->$gatt);
										}

										foreach ($godroll_arr as $godroll_atr) {
											if ($perk == trim($godroll_atr)) {
												$weapon_model->pvp_godrolls++;
												if (!empty($perk_model->godroll)) {
													$perk_model->godroll .= '|pvp';
												} else {
													$perk_model->godroll = 'pvp';
												}
												break 2;
											}
										}
									}
								}

								if (!$perk_model->save()) {
									var_dump($perk_model->getErrors());
									die();
								}
							}
						}

						if (!$weapon_model->save()) {
							var_dump($weapon_model->getErrors());
							die();
						}
					}
					User::updateAll(['last_sync_weapon' => date('Y-m-d H:i:s')], ['id' => Yii::$app->user->identity->id]);
					Yii::$app->user->identity->refresh();
				} else {
					var_dump($model->getErrors());
					die();
				}
			}
		}

		$this->redirect(['site/index']);
	}

	/**
	 * Export weapon with tags
	 */
	public function actionExportWeapons()
	{
		header('Content-type: text/csv');
		header('Content-Disposition: attachment; filename="exported_weapon_(' . date('H-i_d.m.Y') . ').csv"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Content-Description: File Transfer');

		echo "Id,Notes,Tag,Hash\r\n";

		$min_power = $this->power_limit[$this->current_season];

		$weapons = Weapons::find()->all();
		foreach ($weapons as $one_weapon) {
			$tag = "";
			if ($one_weapon->Power_Limit > $min_power) {
				if ($one_weapon->pve_godrolls > 0) {
					$tag .= "pve-" . $one_weapon->pve_godrolls;
				}
				if ($one_weapon->pvp_godrolls > 0) {
					if (!empty($tag)) $tag .= "|";
					$tag .= "pvp-" . $one_weapon->pvp_godrolls;
				}
			}

			if ($one_weapon->usage) {
				if ($one_weapon->usage->pve_usage > 0.5) {
					if (!empty($tag)) $tag .= "|";
					$tag .= "popular-pve";
				}

				if ($one_weapon->usage->pvp_usage > 0.5) {
					if (!empty($tag)) $tag .= "|";
					$tag .= "popular-pvp";
				}
			}
			echo $one_weapon->Id . "," . $tag . ",," . $one_weapon->Hash . "\r\n";
		}
		exit;
	}

	/**
	 * Armor sync
	 */
	public function actionSyncArmor()
	{
		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();
			if (Yii::$app->request->isPost) {
				$model->csv_file = UploadedFile::getInstance($model, 'csv_file');

				if ($model->validate()) {
					Armors::deleteAll(['user_id' => Yii::$app->user->identity->id]);


					$csv = array_map('str_getcsv', file($model->csv_file->tempName));
					array_walk($csv, function (&$a) use ($csv) {
						$a = array_combine($csv[0], $a);
					});
					array_shift($csv); # remove column header
					$armor_attributes = [
						'Id' => 'Id',
						'Name' => 'Name',
						'Hash' => 'Hash',
						'Type' => 'Type',
						'Equippable' => 'Equippable',
						'Masterwork Type' => 'Masterwork Type',
						'Mobility' => 'Mobility (Base)',
						'Recovery' => 'Recovery (Base)',
						'Resilience' => 'Resilience (Base)',
						'Intellect' => 'Intellect (Base)',
						'Discipline' => 'Discipline (Base)',
						'Strength' => 'Strength (Base)',
						'Total' => 'Total (Base)',
						'Power Limit' => 'Power Limit',
					];

					foreach ($csv as $armors) {
						if ($armors['Tier'] != 'Legendary') {
							continue;
						}
						$armor_model = new Armors();
						$armor_model->user_id = Yii::$app->user->identity->id;

						foreach ($armor_attributes as $attribute_key => $attribute_val) {
							$model_attribute = str_replace(' ', '_', $attribute_key);
							if (isset($armors[$attribute_val])) {
								$weapon_attribute = str_replace(['*', '"'], '', $armors[$attribute_val]);
								$armor_model->$model_attribute = $weapon_attribute;
							} else {
								$armor_model->$model_attribute = 0;
							}
						}

						if (!empty($armors['Seasonal Mod'])) {
							if (isset($this->seasons[$armors['Seasonal Mod']])) {
								$armor_model->Season_mod = $this->seasons[$armors['Seasonal Mod']];
							} else {
								throw new Exception('Unknown season: ' . $armors['Seasonal Mod']);
							}
						}


						$attributes = [
							'Mobility',
							'Recovery',
							'Resilience',
							'Intellect',
							'Discipline',
							'Strength',
						];

						for ($i = 0; $i < 5; $i++) {
							$first = $attributes[$i];
							$first_val = $armor_model->$first;

							for ($j = $i + 1; $j < 6; $j++) {
								$second = $attributes[$j];
								$second_val = $armor_model->$second;

								$sum_attr = $first . '_' . $second;
								$sum = $first_val + $second_val;
								$armor_model->$sum_attr = $sum;
							}
						}

						if (!$armor_model->save()) {
							var_dump($armor_model->getErrors());
							die();
						}
					}
					User::updateAll(['last_sync_armor' => date('Y-m-d H:i:s')], ['id' => Yii::$app->user->identity->id]);
					Yii::$app->user->identity->refresh();
				} else {
					var_dump($model->getErrors());
					die();
				}
			}
		}

		$this->redirect(['site/armor']);
	}

	/**
	 * Armor export with perks
	 */
	public function actionExportArmor()
	{
		$count = 3; //count of armors with max attr to store
		$min_sum = 60; //minimum armor attributes sum to store (without modifiers)
		$min_sum_season = 57; //minimum armor attributes sum to store(current and previous season)
		$min_power = $this->power_limit[$this->current_season];

		$types = [
			'Helmet',
			'Gauntlets',
			'Chest Armor',
			'Leg Armor'
		];

		$attributes = [
			'Mobility',
			'Recovery',
			'Resilience',
			'Intellect',
			'Discipline',
			'Strength',
		];

		$max_season = max($this->seasons);

		if (!Yii::$app->user->isGuest) {
			$model = new ExportArmorForm();
			if (Yii::$app->request->isPost) {
				if ($model->load(Yii::$app->request->post()) && $model->validate()) {
					header('Content-type: text/csv');
					header('Content-Disposition: attachment; filename="exported_armor_(' . date('H-i_d.m.Y') . ').csv"');
					header('Content-Transfer-Encoding: binary');
					header('Expires: 0');
					header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
					header('Content-Description: File Transfer');

					echo "Id,Notes,Tag,Hash\r\n";

					$inserted_ids = [];
					foreach ($types as $type) {
						$armors = Armors::find()->where([
							'Type' => $type,
							'Equippable' => $model->equippable,
						])->orderBy([$model->sum => SORT_DESC, 'Power_Limit' => SORT_DESC])->limit($count)->all();

						foreach ($armors as $one_armor) {
							echo $one_armor->Id . ",keep,," . $one_armor->Hash . "\r\n";
							$inserted_ids[] = $one_armor->Id;
						}

						if ($model->export_other == 1) {
							for ($i = 0; $i < 5; $i++) {
								$first = $attributes[$i];

								for ($j = $i + 1; $j < 6; $j++) {
									$second = $attributes[$j];

									$sum_attr = $first . '_' . $second;
									if ($sum_attr != $model->sum) {
										$armors = Armors::find()->where([
											'Type' => $type,
											'Equippable' => $model->equippable,
										])->andWhere(['not in', 'Id', $inserted_ids])->andWhere(['>', 'Power_Limit', $min_power])->orderBy([$sum_attr => SORT_DESC, 'Power_Limit' => SORT_DESC])->limit(2)->all();

										foreach ($armors as $one_armor) {
											echo $one_armor->Id . ",keep,," . $one_armor->Hash . "\r\n";
											$inserted_ids[] = $one_armor->Id;
										}
									}
								}
							}
						}
					}

					if ($min_sum_season > 0) {
						$armors = Armors::find()->where(['Equippable' => $model->equippable])->andWhere([
							'>=',
							'Season_mod',
							$max_season - 1

						])->andWhere([
							'>=',
							'Total',
							$min_sum_season
						])
							->andWhere(['not in', 'Id', $inserted_ids])->all();

						foreach ($armors as $one_armor) {
							echo $one_armor->Id . ",keep,," . $one_armor->Hash . "\r\n";
							$inserted_ids[] = $one_armor->Id;
						}
					}

					if ($min_sum > 0) {
						$armors = Armors::find()->where(['Equippable' => $model->equippable])->andWhere([
							'>=',
							'Total',
							$min_sum
						])
							->andWhere(['not in', 'Id', $inserted_ids])->all();

						foreach ($armors as $one_armor) {
							echo $one_armor->Id . ",keep,," . $one_armor->Hash . "\r\n";
							$inserted_ids[] = $one_armor->Id;
						}
					}

					$other_armors = Armors::find()->where(['not in', 'Id', $inserted_ids])->all();
					foreach ($other_armors as $one_armor) {
						echo $one_armor->Id . ",,," . $one_armor->Hash . "\r\n";
					}
					exit;
				} else {
					var_dump($model->getErrors());
					die();
				}
			}
		}
	}

	/**
	 * God rolls sync
	 */
	public function actionSyncGodrolls()
	{
		if (!Yii::$app->user->isGuest) {
			$model = new SyncForm();
			if (Yii::$app->request->isPost) {
				$model->csv_file = UploadedFile::getInstance($model, 'csv_file');

				if ($model->validate()) {
					ini_set('memory_limit', '6048M');
					ini_set('max_execution_time', 0);

					$weapon_types = [];
					self::get_weapons_type($weapon_types);
					Godroll::deleteAll();

					$xls_to_db = [
						'A' => 'Name',
						'B' => 'Sight_Barrel',
						'C' => 'Mag_Perk',
						'D' => 'Perk_1',
						'E' => 'Perk_2',
						'F' => 'Masterwork',
					];

					$name_fix = [
						"Duke Mk 44" => "Duke Mk. 44",
						"Smuggler’s Word" => "Smuggler's Word",
						"Militia’s Birthright" => "The Militia's Birthright",
						"Mindbender’s Ambition" => "Mindbender's Ambition",
						"Erentil" => "Erentil FR4",
						"Valakdyn" => "Valakadyn",
						"Black Scorpion" => "Black Scorpion-4sr",
						"Proelium-FR 3" => "Proelium FR3",
						"Redrix’s Broadsword" => "Redrix's Broadsword",
						"Nightwatch" => "Night Watch",
						"Bugout Bag" => "Bug-Out Bag",
						"Crimil’s Dagger" => "Crimil's Dagger",
						"Orewing’s Maul" => "Orewing's Maul",
						"Gunnora’s Axe" => "Gunnora's Axe",
						"CALUS Mini Tool" => "CALUS Mini-Tool",
						"Emperor’s Courtesy" => "Emperor's Courtesy",
						"Gahlran’s Right Hand" => "Gahlran's Right Hand",
						"Imperatative" => "Imperative",
						"Sacred Provedance" => "Sacred Provenance",
						"Age Old Bond" => "Age-Old Bond",
						"Old Fashioned" => "The Old Fashioned",
						"Travelers Judgement 5" => "Traveler's Judgment 5",
						"Last Periditon" => "Last Perdition",
						"Black Scorpion 4-SR" => "Black Scorpion-4sr",
					];

					$reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader('Xlsx');
					$reader->setReadDataOnly(TRUE);
					$spreadsheet = $reader->load($model->csv_file->tempName);

					$worksheetNames = $spreadsheet->getSheetNames();
					foreach ($worksheetNames as $sheetIndex => $sheetName) {

						$type = false;
						switch ($sheetName) {
							case "PC PvP":
								$type = 'pvp';
								break;
							case "PC PvE":
								$type = 'pve';
								break;
						}
						if (!$type) continue;
						$sheet = $spreadsheet->getSheet($sheetIndex);
						$sheetData = $sheet->toArray(null, true, true, true);
						foreach ($sheetData as $row) {
							if ($row['A'] != 'Name' && mb_stripos($row['A'], '(curated)') === false && !empty($row['B'])) {
								$gmodel = new Godroll();
								$gmodel->Type = $type;
								foreach ($xls_to_db as $gi => $gc) {
									if (!empty($row[$gi])) {
										$gmodel->$gc = $row[$gi];
									}
								}
								if (isset($name_fix[$gmodel->Name])) {
									$gmodel->Name = $name_fix[$gmodel->Name];
								}
								$name_tmp = strtolower(trim($gmodel->Name));
								if (isset($weapon_types[$name_tmp])) {
									$gmodel->wtype = $weapon_types[$name_tmp]['type'];
									$gmodel->rpm = (string)$weapon_types[$name_tmp]['rounds'];
								} else {
									$gmodel->wtype = null;
									$gmodel->rpm = null;
								}

								$exist_model = Godroll::findOne(['Type' => $gmodel->Type, 'Name' => $gmodel->Name]);
								if ($exist_model) $exist_model->delete();

								if (!$gmodel->save()) {
									var_dump($gmodel->getErrors());
									die();
								}
							}
						}
					}

					file_put_contents('last_sync_godrolls.txt', date("Y-m-d H:i:s"));
				} else {
					var_dump($model->getErrors());
					die();
				}
			}
		}

		$this->redirect(['site/godrolls']);
	}

	/**
	 * Login action.
	 *
	 * @return Response|string
	 */
	public function actionLogin()
	{
		if (!Yii::$app->user->isGuest) {
			return $this->goHome();
		}

		$model = new LoginForm();
		if ($model->load(Yii::$app->request->post()) && $model->login()) {
			return $this->goBack();
		}

		$model->password = '';
		return $this->render('login', [
			'model' => $model,
		]);
	}

	/**
	 * Logout action.
	 *
	 * @return Response
	 */
	public function actionLogout()
	{
		Yii::$app->user->logout();

		return $this->goHome();
	}

	/**
	 * Displays contact page.
	 *
	 * @return Response|string
	 */
	public function actionContact()
	{
		$model = new ContactForm();
		if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
			Yii::$app->session->setFlash('contactFormSubmitted');

			return $this->refresh();
		}
		return $this->render('contact', [
			'model' => $model,
		]);
	}

	/**
	 * Displays about page.
	 *
	 * @return string
	 */
	public function actionAbout()
	{
		return $this->render('about');
	}

	public function onAuthSuccess($client)
	{
		$attributes = $client->getUserAttributes();

		/** @var Auth $auth */
		$auth = Auth::find()->where([
			'source' => $client->getId(),
			'source_id' => $attributes['id'],
		])->one();

		if (Yii::$app->user->isGuest) {
			if ($auth) { // login
				$user = $auth->user;
				Yii::$app->user->login($user);
			} else { // signup
				if (User::find()->where(['email' => $attributes['email']])->exists()) {
					Yii::$app->getSession()->setFlash('error', [
						Yii::t('app', "User with the same email as in {client} account already exists but isn't linked to it. Login using email first to link it.", ['client' => $client->getTitle()]),
					]);
				} else {
					$password = Yii::$app->security->generateRandomString(6);
					$user = new User([
						'username' => $attributes['login'],
						'email' => $attributes['email'],
						'password' => $password,
					]);
					$user->generateAuthKey();
					$user->generatePasswordResetToken();
					$transaction = $user->getDb()->beginTransaction();
					if ($user->save()) {
						$auth = new Auth([
							'user_id' => $user->id,
							'source' => $client->getId(),
							'source_id' => (string)$attributes['id'],
						]);
						if ($auth->save()) {
							$transaction->commit();
							Yii::$app->user->login($user);
						} else {
							print_r($auth->getErrors());
						}
					} else {
						print_r($user->getErrors());
					}
				}
			}
		} else { // user already logged in
			if (!$auth) { // add auth provider
				$auth = new Auth([
					'user_id' => Yii::$app->user->id,
					'source' => $client->getId(),
					'source_id' => $attributes['id'],
				]);
				$auth->save();
			}
		}
	}
}
