<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Godroll;

/**
 * GodrollSearch represents the model behind the search form of `app\models\Godroll`.
 */
class GodrollSearch extends Godroll
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Name', 'Sight_Barrel', 'Mag_Perk', 'Perk_1', 'Perk_2', 'Masterwork', 'Type', 'wtype', 'rpm'], 'safe'],
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
        $query = Godroll::find();

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

        // grid filtering conditions
        $query->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Sight_Barrel', $this->Sight_Barrel])
            ->andFilterWhere(['like', 'Mag_Perk', $this->Mag_Perk])
            ->andFilterWhere(['like', 'Perk_1', $this->Perk_1])
            ->andFilterWhere(['like', 'Perk_2', $this->Perk_2])
            ->andFilterWhere(['like', 'Masterwork', $this->Masterwork]);

        $query->andFilterWhere([
            'Type' => $this->Type,
            'rpm' => $this->rpm
        ]);

        if ($this->wtype!=-1) {
            $query->andFilterWhere([
                'wtype' => $this->wtype
            ]);
        } else {
            $query->andWhere(['is', 'wtype',  new \yii\db\Expression('null')]);
        }

        return $dataProvider;
    }
}
