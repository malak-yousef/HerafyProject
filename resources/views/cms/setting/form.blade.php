<div class="row g-4">

    {{-- معلومات الموقع --}}
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-globe"></i>
                معلومات الموقع
            </div>

            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-6">
                        <label class="form-label">اسم الموقع</label>
                        <input type="text"
                               name="site_name"
                               class="form-control"
                               value="{{ old('site_name', $setting->site_name ?? 'حرفي') }}"
                               required>
                    </div>

                    <div class="col-12">
                        <label class="form-label">وصف الموقع</label>
                        <textarea name="site_description"
                                  class="form-control"
                                  rows="3">{{ old('site_description', $setting->site_description ?? '') }}</textarea>
                    </div>

                </div>
            </div>
        </div>
    </div>


    {{-- إعدادات الحرفيين --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-tools"></i>
                إعدادات الحرفيين
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">
                        عدد العروض المسموحة للحرفي يوميًا
                    </label>

                    <input type="number"
                           name="offers_per_day"
                           class="form-control"
                           min="1"
                           value="{{ old('offers_per_day', $setting->offers_per_day ?? 10) }}"
                           required>

                    <small class="text-muted">
                        عدد العروض التي يستطيع الحرفي تقديمها خلال يوم واحد.
                    </small>
                </div>


                <div class="mb-3">
                    <label class="form-label">
                        أقصى عدد مشاريع للحرفي
                    </label>

                    <input type="number"
                           name="max_portfolios"
                           class="form-control"
                           min="1"
                           value="{{ old('max_portfolios', $setting->max_portfolios ?? 20) }}"
                           required>
                </div>


                <div class="mb-3">
                    <label class="form-label">
                        أقصى عدد صور للمشروع الواحد
                    </label>

                    <input type="number"
                           name="max_portfolio_images"
                           class="form-control"
                           min="1"
                           value="{{ old('max_portfolio_images', $setting->max_portfolio_images ?? 8) }}"
                           required>
                </div>

            </div>
        </div>
    </div>


    {{-- إعدادات الطلبات --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-clipboard-list"></i>
                إعدادات الطلبات
            </div>

            <div class="card-body">

                <div class="mb-3">
                    <label class="form-label">
                        أقصى عدد صور للطلب
                    </label>

                    <input type="number"
                           name="max_order_images"
                           class="form-control"
                           min="1"
                           value="{{ old('max_order_images', $setting->max_order_images ?? 5) }}"
                           required>
                </div>


                <div class="form-check form-switch mb-3">

                    <input class="form-check-input"
                           type="checkbox"
                           name="allow_new_orders"
                           value="1"
                           id="allow_new_orders"
                           {{ old('allow_new_orders', $setting->allow_new_orders ?? true) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="allow_new_orders">

                        السماح بإنشاء طلبات جديدة

                    </label>

                </div>

            </div>
        </div>
    </div>


    {{-- إعدادات العروض --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-handshake"></i>
                إعدادات العروض
            </div>

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label">
                        الحد الأقصى لطول وصف العرض
                    </label>

                    <input type="number"
                           name="max_offer_message_length"
                           class="form-control"
                           min="1"
                           value="{{ old('max_offer_message_length', $setting->max_offer_message_length ?? 1000) }}"
                           required>

                    <small class="text-muted">
                        عدد الأحرف المسموحة في وصف العرض.
                    </small>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        أقل سعر يمكن تقديمه
                    </label>

                    <input type="number"
                           step="0.01"
                           name="min_offer_price"
                           class="form-control"
                           min="0"
                           value="{{ old('min_offer_price', $setting->min_offer_price ?? 1) }}"
                           required>

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        أعلى سعر يمكن تقديمه
                    </label>

                    <input type="number"
                           step="0.01"
                           name="max_offer_price"
                           class="form-control"
                           min="0"
                           value="{{ old('max_offer_price', $setting->max_offer_price ?? 100000) }}"
                           required>

                </div>


                <div class="form-check form-switch">

                    <input class="form-check-input"
                           type="checkbox"
                           name="allow_new_offers"
                           value="1"
                           id="allow_new_offers"
                           {{ old('allow_new_offers', $setting->allow_new_offers ?? true) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="allow_new_offers">

                        السماح للحرفيين بتقديم عروض

                    </label>

                </div>

            </div>
        </div>
    </div>


    {{-- المشاريع --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-images"></i>
                إعدادات المشاريع
            </div>

            <div class="card-body">

                <div class="form-check form-switch">

                    <input class="form-check-input"
                           type="checkbox"
                           name="allow_portfolios"
                           value="1"
                           id="allow_portfolios"
                           {{ old('allow_portfolios', $setting->allow_portfolios ?? true) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="allow_portfolios">

                        السماح للحرفيين بإضافة مشاريع

                    </label>

                </div>

            </div>
        </div>
    </div>


    {{-- التوثيق --}}
    <div class="col-md-6">
        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-user-check"></i>
                إعدادات التوثيق
            </div>

            <div class="card-body">

                <div class="form-check form-switch mb-3">

                    <input class="form-check-input"
                           type="checkbox"
                           name="require_craftsman_verification"
                           value="1"
                           id="require_craftsman_verification"
                           {{ old('require_craftsman_verification', $setting->require_craftsman_verification ?? false) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="require_craftsman_verification">

                        يشترط توثيق الحرفي قبل تقديم العروض

                    </label>

                </div>


                <div class="form-check form-switch">

                    <input class="form-check-input"
                           type="checkbox"
                           name="require_customer_verification"
                           value="1"
                           id="require_customer_verification"
                           {{ old('require_customer_verification', $setting->require_customer_verification ?? false) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="require_customer_verification">

                        يشترط توثيق الزبون

                    </label>

                </div>

            </div>
        </div>
    </div>


    {{-- معلومات التواصل --}}
    <div class="col-md-6">

        <div class="card shadow-sm border-0 h-100">

            <div class="card-header bg-dark text-white">
                <i class="fas fa-phone"></i>
                معلومات التواصل
            </div>

            <div class="card-body">

                <div class="mb-3">

                    <label class="form-label">
                        البريد الإلكتروني
                    </label>

                    <input type="email"
                           name="contact_email"
                           class="form-control"
                           value="{{ old('contact_email', $setting->contact_email ?? '') }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        رقم الهاتف
                    </label>

                    <input type="text"
                           name="contact_phone"
                           class="form-control"
                           value="{{ old('contact_phone', $setting->contact_phone ?? '') }}">

                </div>


                <div class="mb-3">

                    <label class="form-label">
                        رقم واتساب
                    </label>

                    <input type="text"
                           name="whatsapp_number"
                           class="form-control"
                           value="{{ old('whatsapp_number', $setting->whatsapp_number ?? '') }}">

                </div>

            </div>
        </div>

    </div>


    {{-- وضع الصيانة --}}
    <div class="col-12">

        <div class="card shadow-sm border-0">

            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle"></i>
                إعدادات النظام
            </div>

            <div class="card-body">

                <div class="form-check form-switch">

                    <input class="form-check-input"
                           type="checkbox"
                           name="maintenance_mode"
                           value="1"
                           id="maintenance_mode"
                           {{ old('maintenance_mode', $setting->maintenance_mode ?? false) ? 'checked' : '' }}>

                    <label class="form-check-label"
                           for="maintenance_mode">

                        تفعيل وضع الصيانة

                    </label>

                </div>

                <small class="text-danger">
                    عند تفعيل هذا الخيار سيتم اعتبار المنصة في وضع الصيانة.
                </small>

            </div>
        </div>

    </div>

</div>