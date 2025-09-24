# Document 1: Catapult Microgreens Management System - Complete Feature Specification & Use Cases

## Executive Summary

Catapult is a comprehensive microgreens production management system designed to handle the complete lifecycle from seed to sale. The system manages crop production, inventory, customer orders, financial tracking, and employee time management for commercial microgreens operations.

## 1. System Overview & Business Context

### 1.1 Core Business Domain
- **Industry**: Urban agriculture, specifically microgreens production
- **Scale**: Commercial operations managing 100-1000+ trays simultaneously
- **Key Challenges Addressed**:
  - Precise timing of crop stages for consistent harvests
  - Coordination between customer orders and production planning
  - Inventory management for perishable products
  - Batch tracking for food safety compliance
  - Labor cost allocation to specific crops/batches

### 1.2 User Roles & Permissions

**Administrator**
- Full system access
- User management
- Financial reports access
- System configuration
- Data export/import capabilities

**Farm Manager**
- Crop planning and scheduling
- Recipe management
- Inventory oversight
- Order approval
- Report generation

**Grower/Production Staff**
- Crop stage updates
- Harvest recording
- Task completion
- Time clock access
- View growing instructions

**Sales Staff**
- Customer management
- Order creation/editing
- Invoice generation
- Delivery scheduling
- Customer communication

**Warehouse Staff**
- Inventory transactions
- Consumable tracking
- Packaging operations
- Stock counts

## 2. Core Production Features

### 2.1 Crop Lifecycle Management

**Feature**: Multi-stage crop tracking
**Use Case**: Track 50 trays of sunflower microgreens through growth stages
- Create crop batch with 50 trays using Sunflower recipe
- System automatically calculates seed needed (50 trays × 75g/tray = 3.75kg)
- Track through stages: Soaking (8hrs) → Germination (2 days) → Blackout (3 days) → Light (4 days)
- Receive alerts when stage changes are needed
- Record actual vs expected timing for optimization

**Feature**: Batch Management
**Use Case**: Manage multiple batches of same variety
- Group crops planted on same day as single batch
- Assign tray numbers (e.g., Trays 101-150)
- Perform bulk operations (advance stage, harvest)
- Track batch-level metrics and issues

### 2.2 Recipe Management System

**Feature**: Standardized Growing Recipes
**Use Case**: Maintain consistent quality across crops
- Store optimal parameters: seed density, days to maturity, soak time
- Link to specific seed lots and suppliers
- Track success rates by recipe version
- Clone and modify recipes for experimentation
- Calculate expected yields and profitability

**Feature**: Watering Schedules
**Use Case**: Automate irrigation reminders
- Define watering frequency by growth stage
- Adjust for seasonal variations
- Generate daily watering task lists
- Track water usage per crop

### 2.3 Production Planning

**Feature**: Crop Planning Calendar
**Use Case**: Plan weekly production for 20 restaurant customers
- View production capacity by week
- Drag-and-drop crop scheduling
- Automatic lead time calculation
- Color-coded by crop variety or customer
- Conflict detection for space/resource constraints

**Feature**: Order-Driven Planning
**Use Case**: Auto-generate crop plan from customer orders
- System reads upcoming orders
- Calculates backward from delivery dates
- Suggests planting schedule
- Accounts for crop failure buffer (e.g., plant 10% extra)
- Generates material requirements list

## 3. Inventory Management

### 3.1 Seed Inventory

**Feature**: Lot Number Tracking
**Use Case**: Manage multiple seed lots for food safety
- Track supplier, purchase date, expiration
- Monitor germination rates by lot
- Automatic FIFO (First In, First Out) allocation
- Recall capability by lot number
- Link lots to specific harvests for traceability

**Feature**: Reorder Management
**Use Case**: Never run out of critical seeds
- Set minimum stock levels by variety
- Calculate reorder points based on usage patterns
- Compare prices across suppliers
- Track price history and trends
- Generate purchase orders

### 3.2 Consumables Management

**Feature**: Multi-category Inventory
**Use Case**: Track diverse supplies
- Seeds (by variety, lot, supplier)
- Growing media (soil, coco coir, hemp mats)
- Trays (1020, 1010, mesh bottom)
- Packaging (clamshells, bags, labels)
- Cleaning supplies
- Nutrients and amendments

**Feature**: Transaction Logging
**Use Case**: Maintain accurate inventory counts
- Record all ins/outs with timestamps
- Reason codes for adjustments
- Cycle count functionality
- Variance reporting
- Integration with crop creation (auto-deduct)

## 4. Customer & Order Management

### 4.1 Customer Relationship Management

**Feature**: Customer Profiles
**Use Case**: Manage 50+ restaurant accounts
- Contact information and delivery addresses
- Preferred products and quantities
- Pricing tiers and payment terms
- Delivery schedule preferences
- Special instructions (e.g., "knock on back door")
- Purchase history and trends

**Feature**: Customer-Specific Products
**Use Case**: Custom growing for premium clients
- Special seed mixes (e.g., "Chef's Spicy Mix")
- Custom grow times (younger/older microgreens)
- Specific packaging requirements
- Reserved production capacity
- Premium pricing structures

### 4.2 Order Processing

**Feature**: Order Creation Workflow
**Use Case**: Process weekly restaurant orders
- Create single or recurring orders
- Product availability checking
- Automatic pricing based on customer tier
- Delivery date validation
- Order confirmation emails
- Integration with production planning

**Feature**: Recurring Order Templates
**Use Case**: Automate regular customer orders
- Define weekly/bi-weekly templates
- Automatic order generation
- Holiday/vacation scheduling
- Easy modification interface
- Bulk order operations

**Feature**: Order Fulfillment
**Use Case**: Pick, pack, and deliver orders
- Generate pick lists by harvest date
- Pack list with lot numbers
- Delivery route optimization
- Driver manifest generation
- Proof of delivery capture
- Real-time status updates

## 5. Harvest & Post-Harvest Operations

### 5.1 Harvest Management

**Feature**: Harvest Planning
**Use Case**: Coordinate daily harvests
- Harvest ready alerts
- Optimal harvest window tracking
- Labor requirement calculation
- Generate harvest lists by priority
- Quality check requirements
- Yield recording

**Feature**: Yield Tracking
**Use Case**: Monitor production efficiency
- Record actual vs expected yields
- Track by variety, grower, season
- Identify underperforming batches

## 6. Financial Management

### 6.1 Invoicing & Payments

**Feature**: Invoice Generation
**Use Case**: Bill customers efficiently
- Automatic invoice creation from orders
- Batch invoicing for multiple orders
- Payment term management
- Past due notifications
- Payment tracking and allocation
- Stripe payment processing integration

**Feature**: Financial Reporting
**Use Case**: Track business profitability
- Revenue by customer/product
- Cost of goods sold calculation
- Gross margin analysis
- Accounts receivable aging
- Cash flow projections

### 6.2 Cost Tracking

**Feature**: Production Costing
**Use Case**: Calculate true crop costs
- Seed cost allocation
- Labor hours per batch
- Utility cost estimation
- Supply usage tracking
- Overhead allocation
- Profitability by variety

## 7. Employee Management

### 7.1 Time Tracking

**Feature**: Digital Time Clock
**Use Case**: Track employee hours
- Clock in/out with task selection
- Break time tracking
- Overtime calculation
- Time card approval workflow
- Payroll export functionality

**Feature**: Task Assignment
**Use Case**: Manage daily operations
- Create task lists by role
- Priority and due date setting
- Task completion tracking
- Performance metrics
- Recurring task templates

### 7.2 Activity Logging

**Feature**: Comprehensive Audit Trail
**Use Case**: Track all system changes
- User action logging
- Data change history
- Login/logout tracking
- Report access logging
- Compliance audit support

## 8. Alerts & Automation

### 8.1 Production Alerts

**Feature**: Intelligent Notifications
**Use Cases**:
- "10 trays ready to move from blackout to light"
- "Sunflower seeds below reorder point"
- "Customer order due tomorrow not harvested"
- "Germination room temperature out of range"
- "Weekly planning meeting reminder"

### 8.2 Automated Workflows

**Feature**: Business Process Automation
**Use Cases**:
- Auto-create crops from approved orders
- Generate recurring orders every Monday
- Send harvest lists to growers at 6 AM
- Calculate and apply late payment fees
- Update crop stages based on recipe timing

## 9. Reporting & Analytics

### 9.1 Operational Reports

**Dashboard Metrics**:
- Active crops by stage
- Today's harvests
- Tomorrow's deliveries
- Current inventory levels
- Week-over-week production
- Labor hours summary

**Production Reports**:
- Crop success/failure rates
- Yield analysis by variety
- Growing time variance
- Space utilization
- Contamination tracking

### 9.2 Business Intelligence

**Sales Analytics**:
- Customer order patterns
- Product mix analysis
- Seasonal demand trends
- Customer acquisition/churn
- Revenue forecasting

**Financial Analytics**:
- Profit margins by product
- Customer lifetime value
- Cost trend analysis
- ROI by crop variety
- Labor efficiency metrics

## 10. Integration Capabilities

### 10.1 Current Integrations

**Stripe Payment Processing**:
- Credit card processing
- ACH transfers
- Payment plans
- Automatic receipts
- Refund handling

**Calendar Systems**:
- Production calendar sync
- Delivery schedule export
- Task deadline integration
- Team schedule sharing

**PDF Generation**:
- Invoices
- Packing slips
- Reports
- Labels
- Certificates

### 10.2 Data Import/Export

**Import Capabilities**:
- Customer lists (CSV)
- Seed inventory (Excel)
- Historical orders
- Recipe templates

**Export Capabilities**:
- Financial data for QuickBooks
- Inventory reports
- Customer mailing lists
- Production data for analysis
- Compliance documentation

## 11. Mobile & Accessibility

### 11.1 Mobile Features

**Production Floor Access**:
- Stage advancement on tablets
- Harvest recording on phones
- Inventory counts on mobile
- Time clock via mobile

### 11.2 Responsive Design Requirements

- All interfaces work on phones/tablets
- Touch-friendly controls
- Offline capability for critical functions