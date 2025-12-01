<footer class="main-footer">
    <div class="footer-left">
        Copyright &copy; {{ date('Y') }} <div class="bullet"></div> Design By <a href="#">{{ config('app.name') }}</a>
    </div>
    <div class="footer-right">
        {{ $footerRight ?? '' }}
    </div>
</footer>
