<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Weapons;

/**
 * WeaponsSearch represents the model behind the search form of `app\models\Weapons`.
 */
class WeaponsSearch extends Weapons
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Name', 'Hash', 'Type', 'Dmg', 'Masterwork_Type', 'Masterwork_Tier', 'Rpm'], 'safe'],
            [['pve_godrolls', 'pvp_godrolls', 'user_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Weapons::find()->andWhere(['user_id' => \Yii::$app->user->id]);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => false,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('usage');


        $dataProvider->sort->attributes['pve_usage'] = [
            'asc' => ['usage.pve_usage' => SORT_ASC],
            'desc' => ['usage.pve_usage' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['pvp_usage'] = [
            'asc' => ['usage.pvp_usage' => SORT_ASC],
            'desc' => ['usage.pvp_usage' => SORT_DESC],
        ];

        // grid filtering conditions
        $query->andFilterWhere([
            'pve_godrolls' => $this->pve_godrolls,
            'pvp_godrolls' => $this->pvp_godrolls,
            'Rpm' => $this->Rpm
        ]);

        $query->andFilterWhere(['like', 'Id', $this->Id])
            ->andFilterWhere(['like', 'weapons.Name', $this->Name])
            ->andFilterWhere(['like', 'weapons.Hash', $this->Hash])
            ->andFilterWhere(['like', 'Type', $this->Type])
            ->andFilterWhere(['like', 'Dmg', $this->Dmg])
            ->andFilterWhere(['like', 'Masterwork_Type', $this->Masterwork_Type])
            ->andFilterWhere(['like', 'Masterwork_Tier', $this->Masterwork_Tier]);

        return $dataProvider;
    }
}
