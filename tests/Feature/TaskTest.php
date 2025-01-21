<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /**
     * The authenticated user.
     *
     * @var \App\Models\User
     */
    protected $user;

    /**
     * Set up user authentication before each test.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create a user and log in
        $this->user = User::factory()->create();
        $this->actingAs($this->user);
    }

    /** @test */
    public function it_can_display_the_task_index()
    {
        // Create some tasks for the authenticated user
        Task::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->get(route('tasks.index'));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.index');
        $response->assertSee('Tasks');
    }

    /** @test */
    public function it_can_store_a_task()
    {
        $taskData = [
            'title' => 'New Task',
            'description' => 'Task Description',
            'status' => 'pending',
        ];

        $response = $this->post(route('tasks.store'), $taskData);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task created successfully!');
        $this->assertDatabaseHas('tasks', $taskData);
    }

    /** @test */
    public function it_can_show_a_task()
    {
        // Create a task for the authenticated user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(200);
        $response->assertViewIs('tasks.show');
        $response->assertSee($task->title);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        // Create a task for the authenticated user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $updatedData = [
            'title' => 'Updated Task Title',
            'description' => 'Updated description',
            'status' => 'in-progress',
        ];

        $response = $this->put(route('tasks.update', $task), $updatedData);

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task updated successfully!');
        $this->assertDatabaseHas('tasks', $updatedData);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        // Create a task for the authenticated user
        $task = Task::factory()->create(['user_id' => $this->user->id]);

        $response = $this->delete(route('tasks.destroy', $task));

        $response->assertRedirect(route('tasks.index'));
        $response->assertSessionHas('success', 'Task deleted successfully!');
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_cannot_access_another_users_task()
    {
        $otherUser = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->get(route('tasks.show', $task));

        $response->assertStatus(403);
    }
}