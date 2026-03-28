<?php

namespace Tests\Feature;

use App\Enums\BorrowerStatus;
use App\Enums\UserRole;
use App\Models\Borrower;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BorrowerWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_borrower_moves_through_review_chain_and_supports_conditional_decision(): void
    {
        $loanOfficer = User::factory()->create(['role' => UserRole::LOAN_OFFICER]);
        $loanManager = User::factory()->create(['role' => UserRole::LOAN_MANAGER]);
        $generalManager = User::factory()->create(['role' => UserRole::GENERAL_MANAGER]);
        $managingDirector = User::factory()->create(['role' => UserRole::MANAGING_DIRECTOR]);

        $borrower = Borrower::create([
            'user_id' => $loanOfficer->id,
            'full_name' => 'Workflow Test Borrower',
            'nida_number' => '19900101-12345-00001-00',
            'phone' => '0712345678',
            'loan_product' => 'Employment Loan',
            'status' => BorrowerStatus::PENDING_LOAN_MANAGER,
        ]);

        Sanctum::actingAs($loanManager);
        $this->patchJson("/api/v1/borrowers/{$borrower->id}/approve", [
            'riskAssessment' => 'High',
            'riskDescription' => 'Initial risk review completed.',
            'loanManagerRemarks' => 'Forwarding to GM.',
        ])->assertOk()->assertJsonPath('data.status', BorrowerStatus::PENDING_GENERAL_MANAGER->value);

        $this->assertDatabaseHas('borrowers', [
            'id' => $borrower->id,
            'status' => BorrowerStatus::PENDING_GENERAL_MANAGER->value,
            'reviewed_by_loan_manager_id' => $loanManager->id,
            'loan_manager_remarks' => 'Forwarding to GM.',
            'risk_assessment' => 'High',
        ]);

        Sanctum::actingAs($generalManager);
        $this->patchJson("/api/v1/borrowers/{$borrower->id}/approve", [
            'gmRemarks' => 'Secondary review completed.',
        ])->assertOk()->assertJsonPath('data.status', BorrowerStatus::PENDING_MANAGING_DIRECTOR->value);

        $this->assertDatabaseHas('borrowers', [
            'id' => $borrower->id,
            'status' => BorrowerStatus::PENDING_MANAGING_DIRECTOR->value,
            'reviewed_by_gm_id' => $generalManager->id,
            'gm_remarks' => 'Secondary review completed.',
        ]);

        Sanctum::actingAs($managingDirector);
        $this->patchJson("/api/v1/borrowers/{$borrower->id}/approve", [
            'mdRemarks' => 'Need additional documents before final release.',
            'decision' => 'Conditional',
            'decisionRemarks' => 'Submit updated collateral evidence.',
            'decisionName' => 'Managing Director Test',
            'decisionDate' => '2026-03-23',
        ])->assertOk()->assertJsonPath('data.status', BorrowerStatus::CONDITIONAL->value);

        $this->assertDatabaseHas('borrowers', [
            'id' => $borrower->id,
            'status' => BorrowerStatus::CONDITIONAL->value,
            'reviewed_by_md_id' => $managingDirector->id,
            'md_remarks' => 'Need additional documents before final release.',
            'board_decision' => 'Conditional',
            'board_decision_remarks' => 'Submit updated collateral evidence.',
            'board_member_name' => 'Managing Director Test',
        ]);
    }

    public function test_dashboard_enforces_role_gating_and_returns_role_specific_queue(): void
    {
        $loanManager = User::factory()->create(['role' => UserRole::LOAN_MANAGER]);
        $client = User::factory()->create(['role' => UserRole::CLIENT]);

        Borrower::create([
            'full_name' => 'Pending LM',
            'nida_number' => '19900101-12345-00002-00',
            'phone' => '0711111111',
            'loan_product' => 'Jikwamue Loan',
            'status' => BorrowerStatus::PENDING_LOAN_MANAGER,
            'registration_date' => '2026-03-23',
        ]);

        Borrower::create([
            'full_name' => 'Pending GM',
            'nida_number' => '19900101-12345-00003-00',
            'phone' => '0722222222',
            'loan_product' => 'Group Loan',
            'status' => BorrowerStatus::PENDING_GENERAL_MANAGER,
            'registration_date' => '2026-03-22',
        ]);

        Sanctum::actingAs($loanManager);
        $this->getJson('/api/v1/dashboard')
            ->assertOk()
            ->assertJsonPath('data.workflow.target_status', BorrowerStatus::PENDING_LOAN_MANAGER->value)
            ->assertJsonPath('data.workflow.action_required_count', 1)
            ->assertJsonPath('data.workflow.action_required.0.status', BorrowerStatus::PENDING_LOAN_MANAGER->value);

        Sanctum::actingAs($client);
        $this->getJson('/api/v1/dashboard')->assertForbidden();
    }
}
