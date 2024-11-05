@extends('layout')
@section('content')
    <form action="{{ url('/update-review/' . $review->review_id) }}" method="POST">
        @csrf
        @method('PUT')
        <textarea name="comment" required>{{ $review->comment }}</textarea>
        @if ($errors->has('comment'))
            <span class="text-danger">{{ $errors->first('comment') }}</span>
        @endif

        <div class="star-rating">
            <b>ĐÁNH GIÁ:</b>
            @for ($i = 1; $i <= 5; $i++)
                <span class="fa fa-star {{ $i <= $review->rating ? 'checked' : '' }}"
                    data-rating="{{ $i }}"></span>
            @endfor
            <input type="hidden" name="rating" class="rating-value" value="{{ $review->rating }}">
        </div>
        @if ($errors->has('rating'))
            <span class="text-danger">{{ $errors->first('rating') }}</span>
        @endif
        <button type="submit" class="btn btn-default pull-right">Cập nhật</button>
    </form>
@endsection

<style>
    .star-rating {
        display: flex;
    }

    .fa-star {
        font-size: 24px;
        color: #ddd;
        cursor: pointer;
    }

    .fa-star.hover,
    .fa-star.checked {
        color: #f39c12;
    }
</style>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        const stars = document.querySelectorAll('.fa-star');
        let rating = document.querySelector('.rating-value');

        stars.forEach(star => {
            star.addEventListener('mouseover', function() {
                resetStars();
                this.classList.add('hover');
                let prevStar = this.previousElementSibling;

                while (prevStar) {
                    prevStar.classList.add('hover');
                    prevStar = prevStar.previousElementSibling;
                }
            });

            star.addEventListener('mouseout', function() {
                resetStars();
                setStars(rating.value);
            });

            star.addEventListener('click', function() {
                rating.value = this.dataset.rating;
                setStars(rating.value);
            });
        });

        function resetStars() {
            stars.forEach(star => {
                star.classList.remove('hover');
                star.classList.remove('checked');
            });
        }

        function setStars(value) {
            stars.forEach(star => {
                if (star.dataset.rating <= value) {
                    star.classList.add('checked');
                }
            });
        }

        setStars(rating.value);
    });
</script>
