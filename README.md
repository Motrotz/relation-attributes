# Relation Attributes for Laravel

Define Eloquent relationships using PHP attributes for cleaner, more declarative models.

## Features and Benefits

- **Declarative Syntax**: Define relationships using PHP 8 attributes directly on your model class
- **Supports Core Relationships**: BelongsTo, HasOne, HasMany, and BelongsToMany (more coming soon)
- **Cleaner Models**: Reduce boilerplate by eliminating repetitive relationship methods
- **Better Organization**: See all relationships at a glance at the top of your model
- **Zero Configuration**: Works with Laravel's existing relationship system
- **Type Safety**: IDEs can better understand and autocomplete your relationships
- **Flexible Naming**: Auto-generates relation names or specify custom ones

## Installation

```bash
composer require motrotz/relation-attributes
```

## Usage

### Example Setup

Add the `HasRelationAttributes` trait to your Eloquent model and define relationships using attributes. You can be as detailed as needed, or as simple as you want. You can even have multiple relationships of the same type:

```php
use Illuminate\Database\Eloquent\Model;
use RelationAttributes\Concerns\HasRelationAttributes;
use RelationAttributes\Attributes\{BelongsTo, HasOne, HasMany};

#[BelongsTo(Team::class)]
#[BelongsTo(Team::class, 'personalTeam', 'personal_team_id')]
#[HasOne(Profile::class)]
#[HasMany(Post::class, 'articles', 'author_uuid', 'uuid')]
#[HasMany(Notification::class)]
#[HasMany(
    Post::class,
    'publishedPosts',
    where: ['published' => true],
    orderBy: ['created_at' => 'desc'],
    limit: 5
)]
class User extends Model
{
    use HasRelationAttributes;
}

// Now you can access relationships as usual:
$user->team;         // BelongsTo relationship
$user->personalTeam;
$user->profile;      // HasOne relationship
$user->articles;
$user->notifications; // HasMany relationship (auto-pluralized)
$user->publishedPosts; // Filtered & sorted HasMany
```

## Supported Relationships

| Relationship | Status | Example |
|-------------|--------|---------|
| BelongsTo | ✅ Supported | `#[BelongsTo(Team::class)]` |
| HasOne | ✅ Supported | `#[HasOne(Profile::class)]` |
| HasMany | ✅ Supported | `#[HasMany(Post::class)]` |
| BelongsToMany | ✅ Supported | `#[BelongsToMany(Role::class)]` |
| MorphTo | 🚧 Coming Later | Polymorphic relationships |
| MorphOne | 🚧 Coming Later | Polymorphic relationships |
| MorphMany | 🚧 Coming Later | Polymorphic relationships |

## How It Works

The package uses PHP reflection to read attributes defined on your model class during initialization. It then automatically registers these as Eloquent relationships using Laravel's `resolveRelationUsing()` method. This means:

- All Eloquent features work as expected (eager loading, querying, etc.)
- No performance overhead after initialization
- Fully compatible with existing Laravel applications
- Relationships are resolved lazily, just like regular Eloquent


## Testing

```bash
composer test
```

## Documentation

The full documentation can be found on the [project wiki](https://github.com/Motrotz/relation-attributes/wiki).

## External References

- [Laravel Pluralization Documentation](https://laravel.com/docs/12.x/localization#pluralization-language) - Learn more about Laravel's pluralization rules used by this package

## License

MIT License - see the [LICENSE](LICENSE) file for details.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## Credits

- Originally created by [killmails](https://github.com/killmails)
- Maintained by [Motrotz](https://github.com/Motrotz)
