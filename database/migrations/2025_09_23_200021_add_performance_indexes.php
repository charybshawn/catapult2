<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Comprehensive composite indexes for common query patterns

        // 1. CROP PRODUCTION INDEXES
        Schema::table('crop_batches', function (Blueprint $table) {
            // Production planning queries
            $table->index(['planned_harvest_date', 'status', 'recipe_id'], 'idx_crop_batches_harvest_planning');
            $table->index(['current_stage_id', 'status', 'growing_location'], 'idx_crop_batches_stage_location');
            $table->index(['created_at', 'status', 'created_by'], 'idx_crop_batches_timeline');

            // Performance analysis
            $table->index(['recipe_id', 'status', 'actual_yield'], 'idx_crop_batches_yield_analysis');
            $table->index(['planned_plant_date', 'actual_plant_date', 'status'], 'idx_crop_batches_timing_variance');
        });

        Schema::table('crops', function (Blueprint $table) {
            // Production floor queries
            $table->index(['crop_batch_id', 'current_stage_id', 'location'], 'idx_crops_batch_stage_location');
            $table->index(['ready_at', 'status', 'quality_grade'], 'idx_crops_harvest_ready');
            $table->index(['harvested_at', 'harvested_by', 'actual_yield'], 'idx_crops_harvest_tracking');

            // Quality control
            $table->index(['has_contamination', 'is_quarantined', 'status'], 'idx_crops_quality_issues');
            $table->index(['quality_grade', 'harvested_at', 'crop_batch_id'], 'idx_crops_quality_timeline');
        });

        // 2. INVENTORY MANAGEMENT INDEXES
        Schema::table('consumables', function (Blueprint $table) {
            // Stock level monitoring
            $table->index(['current_stock', 'reorder_point', 'is_active'], 'idx_consumables_reorder_alerts');
            $table->index(['category', 'subcategory', 'is_active'], 'idx_consumables_category_lookup');
            $table->index(['supplier_id', 'is_active', 'current_stock'], 'idx_consumables_supplier_stock');
        });

        Schema::table('seed_lots', function (Blueprint $table) {
            // Lot tracking and FIFO
            $table->index(['consumable_id', 'status', 'expiration_date'], 'idx_seed_lots_fifo');
            $table->index(['expiration_date', 'status', 'quantity_remaining'], 'idx_seed_lots_expiration');
            $table->index(['germination_rate', 'status', 'last_used_at'], 'idx_seed_lots_quality');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            // Transaction analysis
            $table->index(['consumable_id', 'transaction_type', 'transaction_date'], 'idx_inventory_usage_patterns');
            $table->index(['performed_by', 'transaction_date', 'transaction_type'], 'idx_inventory_user_activity');
            $table->index(['reference_type', 'reference_id', 'transaction_date'], 'idx_inventory_reference_tracking');
        });

        // 3. CUSTOMER & ORDER INDEXES
        Schema::table('customers', function (Blueprint $table) {
            // Sales analysis
            $table->index(['customer_type', 'status', 'total_lifetime_value'], 'idx_customers_sales_analysis');
            $table->index(['last_order_date', 'status', 'sales_rep_id'], 'idx_customers_activity_tracking');
            $table->index(['delivery_postal_code', 'status', 'customer_type'], 'idx_customers_delivery_routing');
        });

        Schema::table('orders', function (Blueprint $table) {
            // Order fulfillment tracking
            $table->index(['confirmed_delivery_date', 'status', 'customer_id'], 'idx_orders_delivery_schedule');
            $table->index(['production_start_date', 'status', 'assigned_grower_id'], 'idx_orders_production_schedule');
            $table->index(['order_type', 'recurring_frequency', 'parent_order_id'], 'idx_orders_recurring_management');

            // Financial tracking
            $table->index(['payment_status', 'payment_due_date', 'total_amount'], 'idx_orders_payment_tracking');
            $table->index(['customer_id', 'order_date', 'total_amount'], 'idx_orders_customer_history');
        });

        Schema::table('order_items', function (Blueprint $table) {
            // Production allocation
            $table->index(['allocation_status', 'ready_for_harvest_at', 'variety'], 'idx_order_items_harvest_planning');
            $table->index(['allocated_crop_batch_id', 'allocation_status', 'quantity_remaining'], 'idx_order_items_batch_allocation');
            $table->index(['harvest_date', 'lot_number', 'requires_lot_tracking'], 'idx_order_items_lot_tracking');
        });

        // 4. FINANCIAL INDEXES
        Schema::table('invoices', function (Blueprint $table) {
            // Accounts receivable
            $table->index(['due_date', 'status', 'amount_due'], 'idx_invoices_ar_aging');
            $table->index(['customer_id', 'invoice_date', 'total_amount'], 'idx_invoices_customer_billing');
            $table->index(['sent_at', 'status', 'reminder_count'], 'idx_invoices_communication');
        });

        Schema::table('payments', function (Blueprint $table) {
            // Payment processing
            $table->index(['payment_date', 'status', 'payment_method'], 'idx_payments_processing');
            $table->index(['deposited_date', 'is_reconciled', 'amount'], 'idx_payments_reconciliation');
            $table->index(['customer_id', 'payment_date', 'amount'], 'idx_payments_customer_history');
        });

        // 5. PRODUCTION PLANNING INDEXES
        Schema::table('tasks', function (Blueprint $table) {
            // Daily operations
            $table->index(['scheduled_at', 'status', 'assigned_to'], 'idx_tasks_daily_schedule');
            $table->index(['task_type', 'location', 'scheduled_at'], 'idx_tasks_location_schedule');
            $table->index(['target_type', 'target_id', 'status'], 'idx_tasks_target_tracking');

            // Performance monitoring
            $table->index(['due_at', 'status', 'priority'], 'idx_tasks_deadline_monitoring');
            $table->index(['completed_at', 'assigned_to', 'efficiency_score'], 'idx_tasks_performance_tracking');
        });

        Schema::table('alerts', function (Blueprint $table) {
            // Alert management
            $table->index(['status', 'severity', 'created_at'], 'idx_alerts_active_monitoring');
            $table->index(['alert_type', 'source_type', 'source_id'], 'idx_alerts_source_tracking');
            $table->index(['assigned_to', 'status', 'escalation_level'], 'idx_alerts_assignment_tracking');
        });

        // 6. TIME TRACKING INDEXES
        Schema::table('time_clock_entries', function (Blueprint $table) {
            // Payroll processing
            $table->index(['user_id', 'work_date', 'entry_type'], 'idx_time_entries_payroll');
            $table->index(['work_date', 'is_processed', 'status'], 'idx_time_entries_processing');
            $table->index(['active_task_id', 'work_type', 'timestamp'], 'idx_time_entries_task_tracking');
        });

        // 7. HARVEST & YIELD INDEXES
        Schema::table('harvests', function (Blueprint $table) {
            // Yield analysis
            $table->index(['harvest_date', 'overall_quality', 'total_yield'], 'idx_harvests_quality_analysis');
            $table->index(['crop_batch_id', 'harvest_date', 'total_yield'], 'idx_harvests_batch_performance');
            $table->index(['allocation_status', 'best_by_date', 'remaining_amount'], 'idx_harvests_inventory_management');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop composite indexes
        Schema::table('crop_batches', function (Blueprint $table) {
            $table->dropIndex('idx_crop_batches_harvest_planning');
            $table->dropIndex('idx_crop_batches_stage_location');
            $table->dropIndex('idx_crop_batches_timeline');
            $table->dropIndex('idx_crop_batches_yield_analysis');
            $table->dropIndex('idx_crop_batches_timing_variance');
        });

        Schema::table('crops', function (Blueprint $table) {
            $table->dropIndex('idx_crops_batch_stage_location');
            $table->dropIndex('idx_crops_harvest_ready');
            $table->dropIndex('idx_crops_harvest_tracking');
            $table->dropIndex('idx_crops_quality_issues');
            $table->dropIndex('idx_crops_quality_timeline');
        });

        Schema::table('consumables', function (Blueprint $table) {
            $table->dropIndex('idx_consumables_reorder_alerts');
            $table->dropIndex('idx_consumables_category_lookup');
            $table->dropIndex('idx_consumables_supplier_stock');
        });

        Schema::table('seed_lots', function (Blueprint $table) {
            $table->dropIndex('idx_seed_lots_fifo');
            $table->dropIndex('idx_seed_lots_expiration');
            $table->dropIndex('idx_seed_lots_quality');
        });

        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropIndex('idx_inventory_usage_patterns');
            $table->dropIndex('idx_inventory_user_activity');
            $table->dropIndex('idx_inventory_reference_tracking');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex('idx_customers_sales_analysis');
            $table->dropIndex('idx_customers_activity_tracking');
            $table->dropIndex('idx_customers_delivery_routing');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('idx_orders_delivery_schedule');
            $table->dropIndex('idx_orders_production_schedule');
            $table->dropIndex('idx_orders_recurring_management');
            $table->dropIndex('idx_orders_payment_tracking');
            $table->dropIndex('idx_orders_customer_history');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropIndex('idx_order_items_harvest_planning');
            $table->dropIndex('idx_order_items_batch_allocation');
            $table->dropIndex('idx_order_items_lot_tracking');
        });

        Schema::table('invoices', function (Blueprint $table) {
            $table->dropIndex('idx_invoices_ar_aging');
            $table->dropIndex('idx_invoices_customer_billing');
            $table->dropIndex('idx_invoices_communication');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex('idx_payments_processing');
            $table->dropIndex('idx_payments_reconciliation');
            $table->dropIndex('idx_payments_customer_history');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex('idx_tasks_daily_schedule');
            $table->dropIndex('idx_tasks_location_schedule');
            $table->dropIndex('idx_tasks_target_tracking');
            $table->dropIndex('idx_tasks_deadline_monitoring');
            $table->dropIndex('idx_tasks_performance_tracking');
        });

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropIndex('idx_alerts_active_monitoring');
            $table->dropIndex('idx_alerts_source_tracking');
            $table->dropIndex('idx_alerts_assignment_tracking');
        });

        Schema::table('time_clock_entries', function (Blueprint $table) {
            $table->dropIndex('idx_time_entries_payroll');
            $table->dropIndex('idx_time_entries_processing');
            $table->dropIndex('idx_time_entries_task_tracking');
        });

        Schema::table('harvests', function (Blueprint $table) {
            $table->dropIndex('idx_harvests_quality_analysis');
            $table->dropIndex('idx_harvests_batch_performance');
            $table->dropIndex('idx_harvests_inventory_management');
        });

    }
};