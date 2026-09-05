@if(session('success'))

    <div class="flash-message flash-success">

        <div class="flash-content">
            <strong>Success !</strong>

            <span>{{ session('success') }}</span>
        </div>

        <button type="button" class="flash-close"
         onclick="this.parentElement.remove()">
            ×
        </button>
    </div>

@endif

@if(session('error'))

    <div class="flash-message flash-error">
        <div class="flash-content">
            <strong>Error !</strong>
            <span>{{ session('error') }}</span>
        </div>

        <button type="button" class="flash-close"
        onclick="this.parentElement.remove()">
            ×
        </button>
    </div>

@endif

<script>
    setTimeout(()=>{
        const flashMessage = document.querySelector('.flash-message');

        if(flashMessage){
            flashMessage.remove();
        }
    },4000);
</script>
