<div class="{__NODE_ID__}" instance="{__INSTANCE__}">

    <div class="section">
        <div class="row">
            <div class="label">Минимальное наличие</div>
            <div class="control threshold">
                {THRESHOLD_INPUT}
            </div>
        </div>

        <div class="row">
            <div class="label">Минимальное количество товаров в наличии, %</div>
            <div class="control threshold">
                {MIN_PERCENT_INPUT}
            </div>
        </div>

        <div class="row l2">
            <div class="label">Игнорировать товары с нулевой ценой</div>
            <div class="control">
                {ZEROPRICE_CUTOFF_TOGGLE}
            </div>
        </div>
    </div>

    <div class="render">
        <div class="render_button">
            <div class="idle">
                <div class="icon fa fa-angle-double-right"></div>
            </div>
            <div class="proc">
                <div class="progress">
                    <div class="bar"></div>
                    <div class="info">
                        <span class="status"></span>
                        <span class="position"></span>
                        <span class="percent"></span>
                    </div>

                    <div class="break_button">прервать</div>
                </div>
            </div>
        </div>
    </div>

</div>
