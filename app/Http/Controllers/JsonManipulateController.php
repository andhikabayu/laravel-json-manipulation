<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class JsonManipulateController extends Controller
{
    function loadFirstJson()
    {
        $filePath = storage_path('/json_file/first_json.json');
        $jsonContent = file_get_contents($filePath);

        // Check for JSON decoding errors
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
            return response()->json([
                'status' => 0,
                'message' => 'error',
                'error' => true,
                'data' => []
            ]);
        } else {
            // JSON data is successfully loaded and decoded
            return response()->json([
                'status' => 1,
                'message' => 'Data Successfully Retrieved.',
                'error' => false,
                'data' => $data
            ]);
        }
    }

    function loadSecondJson()
    {
        $filePath = storage_path('/json_file/second_json.json');
        $jsonContent = file_get_contents($filePath);

        // Check for JSON decoding errors
        $data = json_decode($jsonContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON decoding error
            // echo 'Error decoding JSON: ' . json_last_error_msg();
            return response()->json([
                'status' => 0,
                'message' => 'error',
                'error' => true,
                'data' => []
            ]);
        } else {
            // JSON data is successfully loaded and decoded
            return response()->json([
                'status' => 1,
                'message' => 'Data Successfully Retrieved.',
                'error' => false,
                'data' => $data
            ]);
        }
    }

    function sortJsonDataByDistanceAsc($jsonData)
    {
        $dataArray = $jsonData;

        // Define the comparison function for sorting
        $compareFunction = function ($a, $b) {
            return $a['ahass_distance'] - $b['ahass_distance'];
        };

        // Sort the array using the comparison function
        usort($dataArray, $compareFunction);

        return $dataArray;
    }

    public function getResult()
    {
        $json1 = $this->loadFirstJson();
        $json2 = $this->loadSecondJson();

        if ($json1->original['error'] || $json2->original['error']) {
            return response()->json(
                [
                    'status' => 0,
                    'error' => true
                ], 404);
        }
        // return response()->json($json2, 200);

        $data1 = $json1->original['data']['data'];
        $data2 = $json2->original['data']['data'];

        $joinedData = collect($data1)->map(function ($item) use ($data2) {
            $matchingItem = collect($data2)->firstWhere('code', $item['booking']['workshop']['code']);

            if ($matchingItem) {
                return array_merge($item, ['workshop' => $matchingItem]);
            } else {
                return array_merge($item, ['workshop' => null]);
            }

            return $item;
        })->toArray();

        $finalArray = collect($joinedData)->map(function ($item) {
            return [
                'name' => $item['name'],
                'email' => $item['email'],
                'booking_number' => $item['booking']['booking_number'] ,
                'book_date' => $item['booking']['book_date'],
                'ahass_code' => $item['booking']['workshop']['code'],
                'ahass_name' => $item['booking']['workshop']['name'],
                'ahass_address' => $item['workshop']['address'] ?? '',
                'ahass_contact' => $item['workshop']['phone_number'] ?? '',
                'ahass_distance' => $item['workshop']['distance'] ?? 0,
                'motorcycle_ut_code' => $item['booking']['motorcycle']['ut_code'],
                'motorcycle' => $item['booking']['motorcycle']['name'],
            ];
        })->toArray();

        $sortedArray = $this->sortJsonDataByDistanceAsc($finalArray);

        return response()->json(
            [
                'status' => 1,
                'message' => 'Data Successfully Retrieved.',
                'data' => $sortedArray
            ], 200);
    }
}
