<?php

namespace App\Http\Controllers\Api;

use App\Models\Psychologist;
use App\Models\Patient;
use App\Models\Relation;
use App\Models\Consult;
use App\Models\ConsultInfo;
use App\Models\NoteQuestion;
use App\Models\NoteAnswer;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ConsultController extends BaseController
{
    public function index()
    {
        $consults = Consult::with('relation.patient', 'relation.psychologist',
                                'consult_info', 'note_questions',
                                'note_questions.note_answers')
                            ->get();

        return $this->respond($consults);
    }

    public function show($id)
    {
        $consult = Consult::with('relation.patient', 'relation.psychologist',
                            'consult_info', 'note_questions',
                            'note_questions.note_answers')
                        ->find($id);
        if(!$consult) {
            return $this->errorNotFound('invalid consult id');
        }

        return $this->respond($consult);
    }

    public function store(Request $request) {
        //Create relation->consult->conult_infos
        $validation = Validator::make($request->all(), [
            'patient_id' => 'required',
            'psychologist_id' => 'required',
            'date' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }

        $relation = Relation::where('patient_id', $request->patient_id)
                                ->where('psychologist_id', $request->psychologist_id)
                                ->first();

        if($relation){
            $relation_id = $relation->id;
        }
        else{
            $patient = Patient::find($request->patient_id);
            if(!$patient){
                return $this->errorNotFound('invalid patient id');
            }
            $psychologist = Psychologist::find($request->psychologist_id);
            if(!$psychologist){
                return $this->errorNotFound('invalid psychologist id');
            }
            $relation = Relation::create([
                'patient_id' => $request->patient_id,
                'psychologist_id' => $request->psychologist_id,
            ]);
            $relation_id = $relation->id;

            $patient->save();
        }

        $last_date = null;
        $consults = Consult::where('relation_id', $relation_id)->orderBy('created_at', 'desc')->get();
        if(count($consults) >= 1){
            $last_consult = $consults->first();
            $last_date = $last_consult->next_date;
        }

        $consult = Consult::create([
            'relation_id' => $relation_id,
            'consult_index' => count($consults)+1,
            'last_date' => $last_date,
            'next_date' => $request->date,
        ]);

        $consult_info = ConsultInfo::create([
            'consult_id' => $consult->id,
            'videocall_date' => $request->date,
        ]);

        return $this->respond($consult);
    }

    public function storeNoteQuestion(Request $request) {
        $validation = Validator::make($request->all(), [
            'consult_id' => 'required',
            'question_text' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }

        $consult = Consult::find($request->consult_id);
        if(!$consult) {
            return $this->errorNotFound('invalid consult id');
        }

        NoteQuestion::create([
            'consult_id' => $consult->id,
            'question_text' => $request->question_text,
        ]);
    }

    public function storeNoteAnswer(Request $request) {
        $validation = Validator::make($request->all(), [
            'note_question_id' => 'required',
            'answer_text' => 'required',
        ]);
        if($validation->fails()) {
            return $this->validationError();
        }

        $note_question = NoteQuestion::find($request->note_question_id);
        if(!$note_question) {
            return $this->errorNotFound('invalid note question id');
        }

        NoteAnswer::create([
            'note_question_id' => $note_question->id,
            'date' => Carbon::now(),
            'answer_text' => $request->answer_text,
        ]);
    }
}
