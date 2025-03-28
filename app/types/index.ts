export interface Asset {
  id: number;
  name: string;
  description: string;
  image_url?: string | null;
  price: number;
  owner_address: string;
  contract_address?: string;
  metadata?: Record<string, any>;
  status: 'active' | 'sold' | 'auction';
  auction_end_time?: string;
  created_at: string;
  updated_at: string;
}

export interface Transaction {
  id: number;
  asset_id: number;
  transaction_hash: string;
  from_address: string;
  to_address: string;
  amount: number;
  type: 'purchase' | 'bid' | 'auction_end';
  status: 'pending' | 'completed' | 'failed';
  metadata?: Record<string, any>;
  created_at: string;
  updated_at: string;
} 