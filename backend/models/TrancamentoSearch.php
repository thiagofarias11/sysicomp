<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Trancamento;

/**
 * TrancamentoSearch represents the model behind the search form of `app\models\Trancamento`.
 */
class TrancamentoSearch extends Trancamento
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'tipo', 'status'], 'integer'],
            [['matricula', 'orientador', 'idAluno', 'dataSolicitacao', 'dataInicio', 'dataInicio0', 'prevTermino', 'dataTermino', 'justificativa', 'documento'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * Part of this method was self-generated by the framework
     * 
     * @author Pedro Frota <pvmf@icomp.ufam.edu.br>
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Trancamento::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,

            //Specifies the criteria for sorting
            'sort' => array(
                'attributes' => array(
                    'matricula' => array(
                        //Specifies the ordering criteria in ascending order, the others have similar operation
                        'asc' => array('j17_aluno.matricula' => SORT_ASC),
                        //Specifies the ordering criteria in descending order, the others have similar operation
                        'desc'=> array('j17_aluno.matricula' => SORT_DESC)
                    ),
                    'idAluno' => array(
                        'asc' => array('j17_aluno.nome' => SORT_ASC),
                        'desc'=> array('j17_aluno.nome' => SORT_DESC)
                    ),
                    'orientador' => array(
                        'asc' => array('j17_user.nome' => SORT_ASC),
                        'desc'=> array('j17_user.nome' => SORT_DESC)
                    ),
                    //'dataSolicitacao',
                    'dataInicio0' => array(
                        'asc' => array('dataInicio' => SORT_ASC),
                        'desc'=> array('dataInicio' => SORT_DESC)
                    ),
                    //'prevTermino',
                    //'dataTermino',
                    'tipo' => array(
                        //Inversion required, because 'Trancamento' is represented by 0, which comes before 1, which represents 'Suspensão'. However, the letter 'S' comes before the letter 'T'.
                        'asc' => array('dataInicio' => SORT_DESC),
                        'desc'=> array('dataInicio' => SORT_ASC)
                    ),
                    'status'
                )
            ),
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //Joins with tables for students and advisors
        //See 'aluno' and 'orientador0' in the 'Trancamento' model for more information
        $query->joinWith('aluno');
        $query->joinWith('orientador0');

        $searchedDataInicio = explode("/", $this->dataInicio0);
        if (sizeof($searchedDataInicio) == 3) {
            $searchedDataInicio = $searchedDataInicio[2]."-".$searchedDataInicio[1]."-".$searchedDataInicio[0];
        }
        else $searchedDataInicio = '';
        
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            //'dataSolicitacao' => $this->dataSolicitacao,
            'dataInicio' => $searchedDataInicio,
            //'prevTermino' => $this->prevTermino,
            //'dataTermino' => $this->dataTermino,
            'tipo' => $this->tipo,
            'j17_trancamentos.status' => $this->status,
        ]);

        $query->andFilterWhere(['like', 'j17_aluno.matricula', $this->matricula])
            ->andFilterWhere(['like', 'j17_aluno.nome', $this->idAluno])
            ->andFilterWhere(['like', 'j17_user.nome', $this->orientador]);
            //->andFilterWhere(['like', 'justificativa', $this->justificativa])
            //->andFilterWhere(['like', 'documento', $this->documento]);

        return $dataProvider;
    }
}
