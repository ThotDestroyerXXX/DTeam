<?php

namespace Tests\Unit\Repositories;

use App\Models\Game;
use App\Models\Genre;
use App\Models\Publisher;
use App\Repositories\GameRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class GameRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected GameRepository $repository;
    protected Publisher $publisher;
    protected Game $game;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = new GameRepository();
        $this->publisher = Publisher::factory()->create();
        $this->game = Game::factory()->create([
            'publisher_id' => $this->publisher->id
        ]);
    }

    public function test_get_publisher_games(): void
    {
        // Act
        $games = $this->repository->getPublisherGames($this->publisher);

        // Assert
        $this->assertNotEmpty($games);
        $this->assertEquals($this->game->id, $games->first()->id);
    }

    public function test_get_publisher_game(): void
    {
        // Act
        $game = $this->repository->getPublisherGame($this->publisher, $this->game->id);

        // Assert
        $this->assertEquals($this->game->id, $game->id);
        $this->assertEquals($this->publisher->id, $game->publisher_id);
    }

    public function test_create_game(): void
    {
        // Arrange
        $data = [
            'title' => 'New Game',
            'brief_description' => 'A brief description',
            'full_description' => 'A full description',
            'price' => 29.99,
            'release_date' => now()->toDateString(),
            'discount' => 10,
            'age_rating_id' => 1,
        ];

        // Act
        $game = $this->repository->createGame($this->publisher, $data);

        // Assert
        $this->assertEquals('New Game', $game->title);
        $this->assertEquals($this->publisher->id, $game->publisher_id);
    }

    public function test_update_game(): void
    {
        // Arrange
        $data = [
            'title' => 'Updated Title',
            'brief_description' => $this->game->brief_description,
            'full_description' => $this->game->full_description,
            'price' => $this->game->price,
            'release_date' => $this->game->release_date,
            'discount' => $this->game->discount_percentage,
            'age_rating_id' => $this->game->age_rating_id,
        ];

        // Act
        $result = $this->repository->updateGame($this->game, $data);

        // Assert
        $this->assertTrue($result);
        $this->game->refresh();
        $this->assertEquals('Updated Title', $this->game->title);
    }

    public function test_get_game_detail_uses_cache(): void
    {
        // Arrange
        Cache::shouldReceive('remember')
            ->once()
            ->andReturn($this->game);

        // Act
        $game = $this->repository->getGameDetail($this->game->id);

        // Assert
        $this->assertEquals($this->game->id, $game->id);
    }

    public function test_attach_genres(): void
    {
        // Arrange
        $genre = Genre::factory()->create();
        $genreIds = [$genre->id];

        // Act
        $this->repository->attachGenres($this->game, $genreIds);

        // Assert
        $this->assertEquals(1, $this->game->genres()->count());
        $this->assertEquals($genre->id, $this->game->genres->first()->id);
    }

    public function test_detach_genres(): void
    {
        // Arrange
        $genre = Genre::factory()->create();
        $this->repository->attachGenres($this->game, [$genre->id]);
        $this->assertEquals(1, $this->game->genres()->count());

        // Act
        $this->repository->detachGenres($this->game);

        // Assert
        $this->assertEquals(0, $this->game->genres()->count());
    }
}
