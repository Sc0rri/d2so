<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ArmorsSearch represents the model behind the search form of `app\models\Armors`.
 */
class ArmorsSearch extends Armors
{

    public $sum;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['Id', 'Name', 'Type', 'Equippable', 'Masterwork_Type', 'sum'], 'safe'],
            [['Mobility', 'Recovery', 'Resilience', 'Intellect', 'Discipline', 'Strength', 'Total', 'user_id', 'Season_mod'], 'integer'],
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
        $query = Armors::find();

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

        if (!empty($this->sum)) {
            $dataProvider->sort->attributes['sum'] = [
                'asc' => [$this->sum => SORT_ASC],
                'desc' => [$this->sum => SORT_DESC],
            ];
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'Mobility' => $this->Mobility,
            'Recovery' => $this->Recovery,
            'Resilience' => $this->Resilience,
            'Intellect' => $this->Intellect,
            'Discipline' => $this->Discipline,
            'Strength' => $this->Strength,
            'Total' => $this->Total,
            'user_id' => $this->user_id,
        ]);

        $query->andFilterWhere(['like', 'Id', $this->Id])
            ->andFilterWhere(['like', 'Name', $this->Name])
            ->andFilterWhere(['like', 'Season_mod', $this->Season_mod])
            ->andFilterWhere(['like', 'Type', $this->Type])
            ->andFilterWhere(['like', 'Equippable', $this->Equippable])
            ->andFilterWhere(['like', 'Masterwork_Type', $this->Masterwork_Type]);

        return $dataProvider;
    }
}
