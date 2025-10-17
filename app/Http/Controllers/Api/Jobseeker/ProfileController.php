<?php

namespace App\Http\Controllers\Api\Jobseeker;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\EducationHistory;
use App\Models\WorkExperience;
use App\Models\OrganizationExperience;
use App\Models\PortfolioHistory;

class ProfileController extends Controller
{
    private function authorizeJobseeker($user)
    {
        if ($user->role_id !== 3) {
            abort(response()->json(['message' => 'Only jobseekers can manage this data.'], 403));
        }
    }


    public function getEducation(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $data = EducationHistory::where('user_id', $user->id)->get();
        return response()->json(['message' => 'Education data retrieved successfully.', 'data' => $data]);
    }

    public function storeEducation(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        // Bisa kirim 1 data atau array data
        $payloads = is_array($request->input(0)) ? $request->all() : [$request->all()];

        $saved = [];
        foreach ($payloads as $data) {
            $validated = validator($data, [
                'institution' => 'required|string|max:255',
                'degree' => 'nullable|string|max:255',
                'start_year' => 'nullable|integer|min:1900|max:2100',
                'end_year' => 'nullable|integer|min:1900|max:2100',
                'gpa' => 'nullable|numeric|between:0,10',
                'description' => 'nullable|string',
            ])->validate();

            $record = EducationHistory::create(array_merge($validated, ['user_id' => $user->id]));
            $saved[] = $record;
        }

        return response()->json(['message' => 'Education data saved successfully.', 'data' => $saved], 201);
    }

    public function updateEducation(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $education = EducationHistory::where('user_id', $user->id)->findOrFail($id);

        $validated = $request->validate([
            'institution' => 'sometimes|string|max:255',
            'degree' => 'nullable|string|max:255',
            'start_year' => 'nullable|integer|min:1900|max:2100',
            'end_year' => 'nullable|integer|min:1900|max:2100',
            'gpa' => 'nullable|numeric|between:0,10',
            'description' => 'nullable|string',
        ]);

        $education->update($validated);
        return response()->json(['message' => 'Education updated successfully.', 'data' => $education]);
    }

    public function deleteEducation(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $education = EducationHistory::where('user_id', $user->id)->findOrFail($id);
        $education->delete();

        return response()->json(['message' => 'Education record deleted successfully.']);
    }

    public function getWork(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $data = WorkExperience::where('user_id', $user->id)->get();
        return response()->json(['message' => 'Work experience retrieved successfully.', 'data' => $data]);
    }

    public function storeWork(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $payloads = is_array($request->input(0)) ? $request->all() : [$request->all()];
        $saved = [];

        foreach ($payloads as $data) {
            $validated = validator($data, [
                'company_name' => 'required|string|max:255',
                'position' => 'required|string|max:255',
                'start_date' => 'nullable|date',
                'end_date' => 'nullable|date',
                'description' => 'nullable|string',
            ])->validate();

            $record = WorkExperience::create(array_merge($validated, ['user_id' => $user->id]));
            $saved[] = $record;
        }

        return response()->json(['message' => 'Work experience saved successfully.', 'data' => $saved], 201);
    }

    public function updateWork(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $work = WorkExperience::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'company_name' => 'sometimes|string|max:255',
            'position' => 'sometimes|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
            'description' => 'nullable|string',
        ]);

        $work->update($validated);
        return response()->json(['message' => 'Work experience updated successfully.', 'data' => $work]);
    }

    public function deleteWork(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $work = WorkExperience::where('user_id', $user->id)->findOrFail($id);
        $work->delete();

        return response()->json(['message' => 'Work experience deleted successfully.']);
    }

    public function getOrganization(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $data = OrganizationExperience::where('user_id', $user->id)->get();
        return response()->json(['message' => 'Organization experience retrieved successfully.', 'data' => $data]);
    }

    public function storeOrganization(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $payloads = is_array($request->input(0)) ? $request->all() : [$request->all()];
        $saved = [];

        foreach ($payloads as $data) {
            $validated = validator($data, [
                'organization_name' => 'required|string|max:255',
                'role' => 'nullable|string|max:255',
                'start_year' => 'nullable|integer|min:1900|max:2100',
                'end_year' => 'nullable|integer|min:1900|max:2100',
                'description' => 'nullable|string',
            ])->validate();

            $record = OrganizationExperience::create(array_merge($validated, ['user_id' => $user->id]));
            $saved[] = $record;
        }

        return response()->json(['message' => 'Organization experience saved successfully.', 'data' => $saved], 201);
    }

    public function updateOrganization(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $organization = OrganizationExperience::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'organization_name' => 'sometimes|string|max:255',
            'role' => 'nullable|string|max:255',
            'start_year' => 'nullable|integer|min:1900|max:2100',
            'end_year' => 'nullable|integer|min:1900|max:2100',
            'description' => 'nullable|string',
        ]);

        $organization->update($validated);
        return response()->json(['message' => 'Organization experience updated successfully.', 'data' => $organization]);
    }

    public function deleteOrganization(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $organization = OrganizationExperience::where('user_id', $user->id)->findOrFail($id);
        $organization->delete();

        return response()->json(['message' => 'Organization experience deleted successfully.']);
    }

    public function getPortfolio(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $data = PortfolioHistory::where('user_id', $user->id)->get();
        return response()->json(['message' => 'Portfolio retrieved successfully.', 'data' => $data]);
    }

    public function storePortfolio(Request $request)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $payloads = is_array($request->input(0)) ? $request->all() : [$request->all()];
        $saved = [];

        foreach ($payloads as $data) {
            $validated = validator($data, [
                'type' => 'required|in:competition,certification,training,publication,achievement',
                'title' => 'required|string|max:255',
                'issuer' => 'nullable|string|max:255',
                'date' => 'nullable|date',
                'attachment' => 'nullable|string|max:255',
                'description' => 'nullable|string',
            ])->validate();

            $record = PortfolioHistory::create(array_merge($validated, ['user_id' => $user->id]));
            $saved[] = $record;
        }

        return response()->json(['message' => 'Portfolio saved successfully.', 'data' => $saved], 201);
    }

    public function updatePortfolio(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $portfolio = PortfolioHistory::where('user_id', $user->id)->findOrFail($id);
        $validated = $request->validate([
            'type' => 'sometimes|in:competition,certification,training,publication,achievement',
            'title' => 'sometimes|string|max:255',
            'issuer' => 'nullable|string|max:255',
            'date' => 'nullable|date',
            'attachment' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $portfolio->update($validated);
        return response()->json(['message' => 'Portfolio updated successfully.', 'data' => $portfolio]);
    }

    public function deletePortfolio(Request $request, $id)
    {
        $user = $request->user();
        $this->authorizeJobseeker($user);

        $portfolio = PortfolioHistory::where('user_id', $user->id)->findOrFail($id);
        $portfolio->delete();

        return response()->json(['message' => 'Portfolio record deleted successfully.']);
    }
}
