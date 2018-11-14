<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\DeleteRequest;
use App\Http\Requests\TrafficSourceAddRequest;
use App\Http\Requests\TrafficSourceEditRequest;
use App\Models\TrafficSource;
use Validator;

class TrafficSourceController extends Controller {

     public function index(Request $request) {
        $response = [
            'title' => "Nguồn traffic"
        ];
        $source_query = TrafficSource::orderBy('source_id', 'ASC');
        $response['sources'] = $source_query->paginate(env('PAGINATE_ITEM', 20));
        return view('trafficSource.index', $response);
    }

    public function doAddSource(TrafficSourceAddRequest $request) {
        try {
            $source = TrafficSource::select(['source_id'])
                    ->where('source_name', $request->input('txt-name'))
                    ->first();
            if (empty($source)) {
                $source = new TrafficSource;
                $source->source_name = $request->input('txt-name');
                $source->source_status = 1;
                $source->source_created_at = microtime(true);
                $source->source_created_by = $request->session()->get('user')->user_id;
                try {
                    $source->save();
                    return redirect()->back()->with('success', 'Thêm nguồn traffic "' . $source->source_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                }
            } else {
                return redirect()->back()->with('error', "Nguồn traffic đã tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function loadSource(Request $request) {
        if ($request->ajax()) {
            $validator = Validator::make($request->all(), [
                        'source_id' => "required|alpha_num",
                            ], [
                        'source_id.required' => "Nguồn traffic không hợp lệ",
                        'source_id.alpha_num' => "Nguồn traffic không hợp lệ",
            ]);
            if (!$validator->fails()) {
                try {
                    $source = TrafficSource::select([
                                        'source_id',
                                        'source_name'
                                    ])
                                    ->where('source_id', $request->input('source_id'))->first();
                    if (!empty($source)) {
                        return response()->json([
                                    "status_code" => 200,
                                    "data" => $source
                        ]);
                    } else {
                        return response()->json([
                                    "status_code" => 404,
                                    "message" => "Nguồn traffic không tồn tại",
                        ]);
                    }
                } catch (\Exception $ex) {
                    return response()->json([
                                "status_code" => 500,
                                "message" => "Lỗi trong quá trình xử lý dữ liệu",
                    ]);
                }
            } else {
                return response()->json([
                            "status_code" => 422,
                            "message" => $validator->errors()->first(),
                ]);
            }
        }
        return redirect()->action('HomeController@index')->with('error', 'Không được truy cập trực tiếp');
    }

    public function doEditSource(TrafficSourceEditRequest $request) {
        try {
            $source = TrafficSource::where('source_id', $request->input('txt-id'))
                    ->first();
            if (!empty($source)) {
                $source_exist = TrafficSource::select(['source_id'])
                        ->where('source_name', $request->input('txt-name'))
                        ->where('source_id', '<>', $source->source_id)
                        ->first();
                if (empty($source_exist)) {
                    $source->source_name = $request->input('txt-name');
                    $source->source_updated_at = microtime(true);
                    $source->source_updated_by = $request->session()->get('user')->user_id;
                    try {
                        $source->save();
                        return redirect()->back()->with('success', 'Sửa nguồn traffic "' . $source->source_name . '" thành công');
                    } catch (\Exception $exc) {
                        return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
                    }
                } else {
                    return redirect()->back()->with('error', "Nguồn traffic đã tồn tại");
                }
            } else {
                return redirect()->back()->with('error', "Nguồn traffic không tồn tại");
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', "Lỗi trong quá trình xử lý dữ liệu");
        }
    }

    public function doDeleteSource(DeleteRequest $request) {
        try {
            $source = TrafficSource::select(['source_name', 'source_id'])
                            ->where('source_id', $request->input('txt-id'))->first();
            if (!empty($source)) {
                try {
                    $source->delete();
                    return redirect()->back()->with('success', 'Xóa nguồn traffic "' . $source->source_name . '" thành công');
                } catch (\Exception $exc) {
                    return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
                }
            } else {
                return redirect()->back()->with('error', 'Nguồn traffic không tồn tại');
            }
        } catch (\Exception $exc) {
            return redirect()->back()->with('error', 'Lỗi trong quá trình xử lý dữ liệu');
        }
    }

}
