<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel untuk destinasi wisata
        Schema::create('destinations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->integer('capacity')->nullable();
            $table->time('opening_hour')->nullable();
            $table->time('closing_hour')->nullable();
            $table->enum('status', ['open', 'closed', 'maintenance'])->default('open');
            $table->timestamps();
        });

        // Tabel untuk hotel
        Schema::create('hotels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->decimal('price_per_night', 10, 2);
            $table->string('image')->nullable();
            $table->integer('star_rating')->nullable();
            $table->timestamps();
        });

        // Tabel untuk restoran
        Schema::create('restaurants', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->string('location');
            $table->float('latitude', 10, 6)->nullable();
            $table->float('longitude', 10, 6)->nullable();
            $table->string('cuisine_type')->nullable();
            $table->string('image')->nullable();
            $table->time('opening_hour')->nullable();
            $table->time('closing_hour')->nullable();
            $table->boolean('has_vegetarian_options')->default(false);
            $table->timestamps();
        });

        // Tabel untuk booking/tiket
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('hotel_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('restaurant_id')->nullable()->constrained()->onDelete('set null');
            $table->dateTime('visit_date');
            $table->integer('number_of_tickets');
            $table->decimal('total_price', 10, 2);
            $table->string('qr_code')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'cancelled', 'completed'])->default('pending');
            $table->boolean('is_package')->default(false);
            $table->timestamps();
        });

        // Tabel untuk transaksi wallet
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->enum('type', ['top_up', 'payment', 'refund'])->default('top_up');
            $table->string('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->timestamps();
        });

        // Tabel untuk favorite/wishlist
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel untuk review dan rating
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->morphs('reviewable'); // Untuk destinasi, hotel, atau restoran
            $table->integer('rating');
            $table->text('comment')->nullable();
            $table->timestamps();
        });

        // Tabel untuk notifikasi
        Schema::create('user_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('type')->default('general');
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Tabel untuk chat support
        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null');
            $table->text('message');
            $table->boolean('is_from_admin')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamps();
        });

        // Tabel untuk paket wisata
        Schema::create('travel_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('price', 10, 2);
            $table->integer('duration_days');
            $table->string('image')->nullable();
            $table->timestamps();
        });

        // Tabel paket wisata dan destinasi
        Schema::create('package_destinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('destination_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel paket wisata dan hotel
        Schema::create('package_hotels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('hotel_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });

        // Tabel paket wisata dan restoran
        Schema::create('package_restaurants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('travel_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('restaurant_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_restaurants');
        Schema::dropIfExists('package_hotels');
        Schema::dropIfExists('package_destinations');
        Schema::dropIfExists('travel_packages');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('user_notifications');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('favorites');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('bookings');
        Schema::dropIfExists('restaurants');
        Schema::dropIfExists('hotels');
        Schema::dropIfExists('destinations');
    }
};